<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LlmGateway
{
    /**
     * Generación no-stream usando OpenAI.
     * @param array $messages
     * @param array $opts
     */
    public function generate(array $messages, array $opts = []): string
    {
        return $this->openaiGenerate($messages, $opts);
    }

    /**
     * Streaming (por ahora no-stream para compatibilidad, o implementar si se requiere).
     * Mantenemos la firma para no romper controladores.
     */
    public function stream(array $messages, array $opts, callable $onDelta): void
    {
        // OpenAI soporta streaming, pero para esta iteración simplificada
        // usamos generate y emitimos todo junto.
        // TODO: Implementar streaming real de OpenAI si el frontend lo requiere.
        try {
            $txt = $this->generate($messages, $opts);
            if ($txt !== '') {
                $onDelta($txt);
            }
        } catch (\Throwable $e) {
            \Log::error('LLM stream failed', ['error' => $e->getMessage()]);
        }
    }

    // ============ OPENAI ============
    private function openaiGenerate(array $messages, array $opts): string
    {
        $apiKey = env('OPENAI_API_KEY');
        if (!$apiKey) {
            throw new \RuntimeException('OPENAI_API_KEY no definida en .env');
        }

        $model = env('OPENAI_MODEL', 'gpt-4o-mini');
        
        // Defaults seguros
        $temperature = (float)($opts['temperature'] ?? 0.2);
        $maxTokens   = (int)  ($opts['max_tokens']  ?? 500);

        $payload = [
            'model'       => $model,
            'messages'    => $messages,
            'temperature' => $temperature,
            'max_tokens'  => $maxTokens,
        ];

        $resp = Http::withToken($apiKey)
            ->timeout(60)
            ->post('https://api.openai.com/v1/chat/completions', $payload);

        if (!$resp->successful()) {
            throw new \RuntimeException('OpenAI Error: '.$resp->status().' '.$resp->body());
        }

        $data = $resp->json();
        return (string)($data['choices'][0]['message']['content'] ?? '');
    }
}