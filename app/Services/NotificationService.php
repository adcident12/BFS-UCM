<?php

namespace App\Services;

use App\Models\NotificationChannel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Dispatch a notification to all active channels that subscribe to this event.
     *
     * @param  array<string, mixed>  $payload
     */
    public function dispatch(string $event, array $payload): void
    {
        $channels = NotificationChannel::where('is_active', true)->get();

        foreach ($channels as $channel) {
            if (! $channel->matchesEvent($event)) {
                continue;
            }

            try {
                match ($channel->type) {
                    'webhook' => $this->sendWebhook($channel->config, $event, $payload),
                    'email' => $this->sendEmail($channel->config, $event, $payload),
                };
            } catch (\Throwable $e) {
                Log::warning("NotificationService: channel [{$channel->id}] failed — {$e->getMessage()}");
            }
        }
    }

    /** @param  array<string, mixed>  $config */
    private function sendWebhook(array $config, string $event, array $payload): void
    {
        $url = $config['url'] ?? null;

        if (! $url) {
            return;
        }

        $body = [
            'event' => $event,
            'payload' => $payload,
            'timestamp' => now()->toIso8601String(),
            'source' => 'UCM',
        ];

        $request = Http::timeout(10)->acceptJson();

        if (! empty($config['secret'])) {
            $signature = hash_hmac('sha256', json_encode($body), $config['secret']);
            $request = $request->withHeader('X-UCM-Signature', $signature);
        }

        $request->post($url, $body);
    }

    /** @param  array<string, mixed>  $config */
    private function sendEmail(array $config, string $event, array $payload): void
    {
        $to = $config['to'] ?? [];

        if (empty($to)) {
            return;
        }

        $subject = '[UCM] '.($payload['description'] ?? $event);
        $lines = ["**Event:** `{$event}`", ''];

        foreach ($payload as $key => $value) {
            if (is_scalar($value)) {
                $lines[] = "**{$key}:** {$value}";
            }
        }

        $lines[] = '';
        $lines[] = 'เวลา: '.now()->format('d/m/Y H:i:s');

        $text = implode("\n", $lines);

        Mail::raw($text, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }
}
