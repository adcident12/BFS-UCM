<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class UcmFeatureOverride extends Model
{
    protected $primaryKey = 'feature_key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'feature_key',
        'min_level',
        'updated_by',
    ];

    protected $casts = [
        'min_level' => 'integer',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(UcmUser::class, 'updated_by');
    }

    /**
     * Resolve the effective min_level for a feature (config default → DB override).
     * Result is cached for 5 minutes to avoid repeated DB hits on every request.
     */
    public static function getEffectiveLevel(string $featureKey): int
    {
        return Cache::remember("ucm_feature_level:{$featureKey}", 300, function () use ($featureKey) {
            $override = static::find($featureKey);
            if ($override) {
                return (int) $override->min_level;
            }

            return (int) (config("ucm_features.{$featureKey}.default_level") ?? 2);
        });
    }

    /**
     * Clear the cached level for a single feature after an override is saved/deleted.
     */
    public static function clearCache(string $featureKey): void
    {
        Cache::forget("ucm_feature_level:{$featureKey}");
    }
}
