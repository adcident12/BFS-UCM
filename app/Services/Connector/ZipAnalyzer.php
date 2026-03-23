<?php

namespace App\Services\Connector;

use Illuminate\Http\UploadedFile;
use ZipArchive;

class ZipAnalyzer
{
    private const MAX_FILES        = 500;
    private const MAX_UNCOMPRESSED = 100 * 1024 * 1024; // 100 MB
    private const MAX_FILE_SIZE    = 512 * 1024;         // 512 KB per file

    private const ALLOWED_EXTENSIONS = [
        // Web / scripting
        'php', 'py', 'rb',
        // JS / TS
        'ts', 'js', 'mjs', 'cjs',
        // ORM / schema DSL
        'prisma', 'graphql',
        // JVM
        'java', 'kt', 'groovy',
        // .NET
        'cs',
        // Go
        'go',
        // SQL & migrations
        'sql',
        // Config / docs
        'xml', 'yaml', 'yml', 'json', 'md',
    ];

    private const RELEVANT_PATTERNS = [
        // Migration files
        '/migrat/i', '/schema/i',
        // ORM / model files
        '/model/i', '/entit/i', '/domain/i',
        // DB config / connection (รองรับ legacy PHP ที่ตั้งชื่อต่างกัน)
        '/database/i', '/db[._-]config/i', '/db_?context/i',
        '/db[._-]connect/i', '/connect(ion)?\./i',
        // Java persistence
        '/persist/i', '/repositor/i',
        // Specific file names
        '/schema\.rb$/i', '/\.prisma$/i', '/db\.py$/i',
        '/sequelize/i', '/typeorm/i', '/gorm/i',
    ];

    private const PII_KEYS = '/password|passwd|secret|api_key|token|private_key/i';

    public function analyze(UploadedFile $file): array
    {
        $zip = new ZipArchive();
        if ($zip->open($file->getRealPath()) !== true) {
            throw new \RuntimeException('ไม่สามารถเปิดไฟล์ ZIP ได้');
        }

        $totalUncompressed = 0;
        $migrationFiles    = [];
        $otherFiles        = [];
        $fileCount         = $zip->numFiles;

        for ($i = 0; $i < min($fileCount, self::MAX_FILES); $i++) {
            $stat = $zip->statIndex($i);
            if ($stat === false) {
                continue;
            }

            $name = $stat['name'];

            if (str_contains($name, '..') || str_ends_with($name, '/')) {
                continue;
            }

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (! in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
                continue;
            }

            $size = $stat['size'];
            if ($size > self::MAX_FILE_SIZE) {
                continue;
            }

            $totalUncompressed += $size;
            if ($totalUncompressed > self::MAX_UNCOMPRESSED) {
                break;
            }

            $content = $zip->getFromIndex($i);
            if ($content === false) {
                continue;
            }

            $content     = $this->sanitizeContent($content);
            $isMigration = (bool) preg_match('/migrat|schema\.rb|\.prisma$|alembic/i', $name);
            $isRelevant  = false;

            foreach (self::RELEVANT_PATTERNS as $pattern) {
                if (preg_match($pattern, $name)) {
                    $isRelevant = true;
                    break;
                }
            }

            if (! $isRelevant) {
                continue;
            }

            if ($isMigration) {
                $migrationFiles[$name] = $content;
            } else {
                $otherFiles[$name] = $content;
            }
        }

        $zip->close();

        $files = array_merge($migrationFiles, $otherFiles);

        // Fallback สำหรับ legacy / no-framework projects:
        // ถ้าไม่มีไฟล์ผ่านกรองเลย ให้ re-scan เฉพาะ .php/.sql เพื่อตรวจ framework
        if (empty($files)) {
            $files = $this->scanFallbackFiles($file);
        }

        $framework = $this->detectFramework($files);

        return [
            'framework'  => $framework,
            'files'      => $files,
            'file_count' => $fileCount,
        ];
    }

    /**
     * Fallback scan: อ่านไฟล์ .php/.sql ทุกไฟล์ (สูงสุด 30 ไฟล์)
     * ใช้เมื่อไม่มีไฟล์ผ่าน RELEVANT_PATTERNS — รองรับ legacy projects ที่ไม่มีโครงสร้างมาตรฐาน
     *
     * @return array<string, string>
     */
    private function scanFallbackFiles(UploadedFile $file): array
    {
        $zip = new ZipArchive();
        if ($zip->open($file->getRealPath()) !== true) {
            return [];
        }

        $result    = [];
        $fileCount = $zip->numFiles;
        $limit     = 30;

        for ($i = 0; $i < $fileCount && count($result) < $limit; $i++) {
            $stat = $zip->statIndex($i);
            if ($stat === false) {
                continue;
            }

            $name = $stat['name'];
            if (str_contains($name, '..') || str_ends_with($name, '/')) {
                continue;
            }

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (! in_array($ext, ['php', 'sql'], true)) {
                continue;
            }

            if ($stat['size'] > self::MAX_FILE_SIZE) {
                continue;
            }

            $content = $zip->getFromIndex($i);
            if ($content !== false) {
                $result[$name] = $this->sanitizeContent($content);
            }
        }

        $zip->close();

        return $result;
    }

    private function sanitizeContent(string $content): string
    {
        return preg_replace_callback(
            '/(' . trim(self::PII_KEYS, '/i') . ')\s*[=:]\s*["\']?(\S+?)["\']?(?=[\s,;\)\n]|$)/i',
            fn ($m) => $m[1] . '=[REDACTED]',
            $content
        ) ?? $content;
    }

    /**
     * ตรวจจับ framework โดยใช้ score-based approach
     * ทั้งชื่อไฟล์และเนื้อหาถูกนำมาใช้เป็น signal
     */
    private function detectFramework(array $files): string
    {
        if (empty($files)) {
            return 'Unknown';
        }

        // รวมชื่อไฟล์ + เนื้อหาทั้งหมดเป็น string เดียวสำหรับ matching
        $fileNames = implode("\n", array_keys($files));
        $combined  = $fileNames . "\n" . implode("\n", $files);

        $scores = [];

        // ── PHP Frameworks ────────────────────────────────────────────────
        $scores['Laravel'] = $this->scoreSignals($combined, [
            'Illuminate\\'          => 10,
            'php artisan'           => 10,
            'Schema::create'        => 9,
            'Schema::table'         => 6,
            '$table->id()'          => 7,
            '$table->timestamps()'  => 6,
            'HasMany'               => 4,
            'BelongsTo'             => 4,
            'use Eloquent'          => 6,
            'extends Model'         => 4,
        ]);

        $scores['CodeIgniter'] = $this->scoreSignals($combined, [
            'CI_Controller'         => 10,
            'CI_Model'              => 10,
            'extends CI_'           => 9,
            '$this->db->get('       => 9,
            '$this->db->insert('    => 7,
            '$this->load->model'    => 8,
            'BASEPATH'              => 6,
            'APPPATH'               => 5,
            'defined(\'BASEPATH\')' => 7,
        ]);

        $scores['Symfony'] = $this->scoreSignals($combined, [
            'Symfony\\'             => 10,
            'Doctrine\\'            => 10,
            '@ORM\\Entity'          => 10,
            '@ORM\\Column'          => 8,
            '@ORM\\ManyToOne'       => 8,
            'EntityManagerInterface' => 8,
            'extends AbstractMigration' => 8,
        ]);

        $scores['Yii2'] = $this->scoreSignals($combined, [
            'yii\\db\\Migration'    => 10,
            'yii\\base\\Model'      => 10,
            'yii\\db\\ActiveRecord' => 10,
            'Yii::$app'             => 8,
            'yii\\'                 => 7,
        ]);

        $scores['CakePHP'] = $this->scoreSignals($combined, [
            'Cake\\'                => 10,
            'CakePHP'               => 8,
            'extends AppModel'      => 8,
            'CakeEvent'             => 7,
            '$this->Model->find'    => 7,
        ]);

        $scores['Slim (PHP)'] = $this->scoreSignals($combined, [
            'Slim\\'                => 10,
            'Slim\App'              => 10,
            '$app->get('            => 6,
            'DI\ContainerBuilder'   => 6,
        ]);

        // ── Python Frameworks ─────────────────────────────────────────────
        $scores['Django'] = $this->scoreSignals($combined, [
            'django.db'             => 10,
            'from django'           => 8,
            'models.Model'          => 9,
            'migrations.Migration'  => 10,
            'models.CharField'      => 7,
            'models.ForeignKey'     => 8,
            'models.IntegerField'   => 5,
            'models.DateTimeField'  => 5,
        ]);

        $scores['Flask/SQLAlchemy'] = $this->scoreSignals($combined, [
            'from flask'            => 10,
            'Flask('                => 9,
            'SQLAlchemy'            => 10,
            'db.Model'              => 9,
            'db.Column('            => 7,
            'db.ForeignKey('        => 7,
            'db.relationship('      => 6,
        ]);

        $scores['FastAPI'] = $this->scoreSignals($combined, [
            'from fastapi'          => 10,
            'FastAPI()'             => 10,
            'SQLModel'              => 9,
            'from sqlmodel'         => 9,
            'alembic'               => 7,
            'AsyncSession'          => 6,
        ]);

        // ── Ruby ──────────────────────────────────────────────────────────
        $scores['Ruby on Rails'] = $this->scoreSignals($combined, [
            'schema.rb'             => 10,
            'ActiveRecord::Migration' => 10,
            'ActiveRecord::Base'    => 10,
            'ApplicationRecord'     => 9,
            'create_table'          => 7,
            'belongs_to'            => 6,
            'has_many'              => 6,
            'add_column'            => 4,
        ]);

        // ── JavaScript / TypeScript ───────────────────────────────────────
        $scores['Prisma'] = $this->scoreSignals($combined, [
            'datasource db'         => 10,
            'generator client'      => 10,
            'provider = "prisma'    => 9,
            '@id'                   => 5,
            '@relation('            => 7,
            'PrismaClient'          => 8,
            '.prisma'               => 8,
        ]);

        $scores['TypeORM'] = $this->scoreSignals($combined, [
            'typeorm'               => 10,
            '@Entity()'             => 10,
            '@Column()'             => 8,
            '@ManyToOne'            => 8,
            '@OneToMany'            => 8,
            '@PrimaryGeneratedColumn' => 9,
            'DataSource'            => 6,
            'createConnection'      => 6,
        ]);

        $scores['Sequelize'] = $this->scoreSignals($combined, [
            'sequelize'             => 10,
            'DataTypes.'            => 9,
            'Model.init('           => 9,
            'queryInterface.createTable' => 9,
            'Sequelize.define('     => 9,
            'belongsTo('            => 5,
            'hasMany('              => 5,
        ]);

        $scores['NestJS'] = $this->scoreSignals($combined, [
            '@nestjs/'              => 10,
            '@Module()'             => 8,
            '@Injectable()'         => 7,
            'TypeOrmModule'         => 8,
            '@Controller('          => 6,
        ]);

        $scores['Knex.js'] = $this->scoreSignals($combined, [
            'knex('                 => 10,
            'knex.schema'           => 10,
            'table.increments('     => 8,
            'table.string('         => 6,
            'table.integer('        => 6,
            'migrations/'           => 4,
        ]);

        // ── Java / JVM ────────────────────────────────────────────────────
        $scores['Spring/JPA'] = $this->scoreSignals($combined, [
            '@Entity'               => 10,
            'javax.persistence'     => 10,
            'jakarta.persistence'   => 10,
            '@Table'                => 8,
            '@Column'               => 7,
            'JpaRepository'         => 10,
            '@ManyToOne'            => 8,
            'Hibernate'             => 7,
            'SpringApplication'     => 6,
        ]);

        $scores['MyBatis'] = $this->scoreSignals($combined, [
            'mybatis'               => 10,
            '<mapper'               => 10,
            '<resultMap'            => 9,
            '<select id='           => 7,
            'SqlSessionFactory'     => 9,
            'MapperScan'            => 8,
        ]);

        // ── .NET ──────────────────────────────────────────────────────────
        $scores['ASP.NET/EF'] = $this->scoreSignals($combined, [
            'DbContext'             => 10,
            'DbSet<'                => 10,
            'EntityFramework'       => 10,
            'Microsoft.EntityFrameworkCore' => 10,
            '[Table('               => 7,
            '[Key]'                 => 6,
            'modelBuilder'          => 7,
            'HasForeignKey'         => 7,
            'OnModelCreating'       => 7,
        ]);

        // ── Go ────────────────────────────────────────────────────────────
        $scores['Go/GORM'] = $this->scoreSignals($combined, [
            'gorm.io'               => 10,
            'gorm.Model'            => 10,
            'db.AutoMigrate'        => 9,
            'gorm:"'                => 7,
            'database/sql'          => 6,
            'sql.Open('             => 5,
        ]);

        // ── Legacy / No Framework ─────────────────────────────────────────
        $scores['Plain PHP (Legacy)'] = $this->scoreSignals($combined, [
            'mysql_connect('        => 10,
            'mysql_query('          => 10,
            'mysql_fetch_array('    => 8,
            'mysqli_connect('       => 9,
            'mysqli_query('         => 8,
            'new PDO('              => 7,
            'pg_connect('           => 7,
            'mssql_connect('        => 9,
            'odbc_connect('         => 7,
            'oci_connect('          => 7,
        ]);

        $scores['Plain SQL'] = $this->scoreSignals($combined, [
            'CREATE TABLE'          => 7,
            'ALTER TABLE'           => 4,
            'PRIMARY KEY'           => 5,
            'FOREIGN KEY'           => 7,
            'REFERENCES'            => 5,
            'CREATE INDEX'          => 4,
        ]);

        // ── Pick winner ───────────────────────────────────────────────────
        arsort($scores);
        $winner      = (string) array_key_first($scores);
        $winnerScore = $scores[$winner];

        // Plain SQL สามารถมี score สูงได้แม้ใน framework project
        // ถ้า winner เป็น Plain SQL แต่มี framework อื่น score ≥ 6 ให้เลือก framework แทน
        if ($winner === 'Plain SQL') {
            foreach ($scores as $name => $score) {
                if ($name !== 'Plain SQL' && $name !== 'Plain PHP (Legacy)' && $score >= 6) {
                    return $name;
                }
            }
        }

        if ($winnerScore < 8) {
            return 'Unknown';
        }

        return $winner;
    }

    /**
     * นับ score จาก signals ที่พบใน content
     * signal => weight
     *
     * @param  array<string, int>  $signals
     */
    private function scoreSignals(string $content, array $signals): int
    {
        $total = 0;
        foreach ($signals as $signal => $weight) {
            if (str_contains($content, $signal)) {
                $total += $weight;
            }
        }

        return $total;
    }
}
