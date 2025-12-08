<?php

namespace App\Jobs;

use App\Models\KnowledgeChunk;
use App\Services\EmbeddingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

class EmbedChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * La cantidad de veces que el job intentará ejecutarse antes de fallar definitivamente.
     * Aumentamos esto para darle margen a la API de recuperarse.
     */
    public $tries = 10;

    /**
     * Calcula el número de segundos a esperar antes de reintentar el job.
     * Estrategia "Exponential Backoff": espera 5s, luego 15s, luego 30s, etc.
     */
    public function backoff(): array
    {
        return [5, 15, 30, 60, 120];
    }

    public function __construct(public int $chunkId)
    {
    }

    public function handle(EmbeddingService $emb): void
    {
        $chunk = KnowledgeChunk::findOrFail($this->chunkId);

        try {
            // Intentamos generar el embedding
            $vec = $emb->embed($chunk->content);

        } catch (Throwable $e) {
            // Si el error es por Rate Limit (429), Laravel usará el método backoff() automáticamente al relanzar la excepción.
            Log::warning("EmbedChunkJob retry [Chunk: {$this->chunkId}]: " . $e->getMessage());
            
            // Es CRÍTICO lanzar la excepción nuevamente para que Laravel sepa que falló y programe el reintento.
            throw $e;
        }

        if (!$vec) {
            throw new \RuntimeException('Embedding vacío recibido del servicio');
        }

        // Si todo salió bien, guardamos
        $chunk->embedding = json_encode($vec);
        $chunk->embedded_at = now();
        $chunk->save();

        // Lógica para actualizar el estado del Source (Padre)
        $this->updateSourceStatus($chunk);
    }

    /**
     * Método auxiliar para manejar la actualización del Source
     * y mantener el handle() limpio.
     */
    protected function updateSourceStatus($chunk): void
    {
        $src = \App\Models\Source::find($chunk->source_id);
        
        if ($src && Schema::hasColumn('sources', 'embedded_count') && Schema::hasColumn('sources', 'chunks_count')) {
            $src->increment('embedded_count');

            // Verificamos si ya se completaron todos los chunks
            if ((int) $src->embedded_count >= (int) $src->chunks_count) {
                $src->update(['status' => 'ready']);
            } else if ($src->status !== 'embedding') {
                $src->update(['status' => 'embedding']);
            }
        }
    }
}