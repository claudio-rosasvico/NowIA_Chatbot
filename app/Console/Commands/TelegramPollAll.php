<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\ChannelIntegration;
use App\Models\Conversation;
use App\Services\ChatService;
// Use Bot explicitly to avoid helper dependency issues if autoload fails
use App\Models\Bot;

class TelegramPollAll extends Command
{
    protected $signature = 'telegram:poll-all {--timeout=25}';
    protected $description = 'Long polling para TODOS los bots de Telegram habilitados (usando ChannelIntegration)';

    public function handle(ChatService $chat): int
    {
        $timeout = (int) $this->option('timeout');

        $this->info("Iniciando poll-all de Telegram...");

        while (true) {
            // Iterar sobre integraciones activas
            $integrations = ChannelIntegration::where('channel', 'telegram')
                ->where('enabled', true)
                ->get();

            foreach ($integrations as $ci) {
                try {
                    $cfg = $ci->config ?? [];
                    $token = $cfg['token'] ?? null;

                    if (!$token)
                        continue;

                    $offset = isset($cfg['last_update_id']) ? $cfg['last_update_id'] + 1 : null;
                    $url = "https://api.telegram.org/bot{$token}/getUpdates";

                    try {
                        $res = Http::timeout($timeout + 5)->get($url, array_filter([
                            'timeout' => $timeout,
                            'offset' => $offset,
                        ]));
                    } catch (\Exception $e) {
                        // Si falla conexión, ignoramos este ciclo
                        continue;
                    }

                    if (!$res->ok())
                        continue;

                    $updates = $res->json('result') ?? [];
                    $maxUpdateId = 0;

                    foreach ($updates as $u) {
                        try {
                            $uId = $u['update_id'];
                            $maxUpdateId = max($maxUpdateId, $uId);

                            $msg = $u['message'] ?? $u['edited_message'] ?? null;
                            if (!$msg)
                                continue;

                            $chatId = $msg['chat']['id'];
                            $text = trim($msg['text'] ?? '');
                            if ($text === '')
                                continue;

                            // Resolución del Bot protegida
                            // Intentamos usar el helper, si no manual
                            if (function_exists('ensure_default_bot')) {
                                $botModel = ensure_default_bot('telegram', $ci->organization_id);
                            } else {
                                $botModel = Bot::firstOrCreate([
                                    'organization_id' => $ci->organization_id,
                                    'channel' => 'telegram',
                                    'name' => 'Telegram Default',
                                ], [
                                    'is_default' => true,
                                    'config' => [
                                        'language' => 'es',
                                        'system_prompt' => 'Eres un asistente útil.',
                                        'temperature' => 0.5,
                                        'max_tokens' => 500,
                                        'retrieval_mode' => 'semantic',
                                    ]
                                ]);
                            }

                            // Conversación
                            $conv = Conversation::firstOrCreate([
                                'organization_id' => $ci->organization_id,
                                'channel' => 'telegram',
                                'external_id' => (string) $chatId,
                            ], [
                                'started_at' => now(),
                                'bot_id' => $botModel->id,
                            ]);

                            // Chat Service Response
                            $resp = $chat->handle($conv->id, $text, 'telegram');
                            $reply = collect($resp['messages'])->last()['content'] ?? '…';

                            // Enviar respuesta (siempre try-catch para evitar crash por error de red en envio)
                            try {
                                Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                                    'chat_id' => $chatId,
                                    'text' => $reply,
                                ]);
                            } catch (\Exception $sendEx) {
                                \Log::error("Telegram sendMessage error: " . $sendEx->getMessage());
                            }

                        } catch (\Throwable $t) {
                            \Log::error("TelegramPollAll update processing error: " . $t->getMessage());
                        }
                    }

                    // Guardar offset
                    if (!empty($updates)) {
                        $currentOffset = $cfg['last_update_id'] ?? 0;
                        if ($maxUpdateId > $currentOffset) {
                            $cfg['last_update_id'] = $maxUpdateId;
                            $ci->config = $cfg;
                            $ci->save();
                        }
                    }

                } catch (\Throwable $outer) {
                    \Log::error("TelegramPollAll integration loop error: " . $outer->getMessage());
                    // Dormir un poco si hay error grave para evitar spam de logs
                    sleep(1);
                }
            }
            sleep(1);
        }

        return self::SUCCESS;
    }
}
