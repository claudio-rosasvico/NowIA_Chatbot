<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EmbeddingService
{
    /**
     * Genera embeddings usando OpenAI (text-embedding-3-small).
     * @param string $text
     * @return array<float>
     */
    public function embed(string $text): array
    {
        $api = env('OPENAI_API_KEY');
        if (!$api) {
            throw new \RuntimeException('OPENAI_API_KEY no definida en .env');
        }

        // Modelo optimizado por defecto
        $model = env('OPENAI_EMBEDDING_MODEL', 'text-embedding-3-small');

        $response = Http::timeout(30)
            ->withToken($api)
            ->post('https://api.openai.com/v1/embeddings', [
                'model' => $model,
                'input' => $text,
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException('OpenAI Embeddings Error: ' . $response->body());
        }

        return array_map('floatval', $response->json('data.0.embedding') ?? []);
    }

    // Alias para compatibilidad si se usa en otros lados
    public function embedText(string $text): array
    {
        return $this->embed($text);
    }
}
