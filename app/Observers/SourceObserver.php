<?php

namespace App\Observers;

use App\Models\Source;
use App\Models\KnowledgeChunk;

class SourceObserver
{
    /**
     * Handle the Source "deleted" event.
     */
    public function deleted(Source $source): void
    {
        // When a source is soft-deleted, we soft-delete its chunks too.
        // This ensures data consistency if we ever restore the source.
        // Note: Logic allows for forceDeleting chunks if prefered, but keeping sync is safer.
        KnowledgeChunk::where('source_id', $source->id)->delete();
    }

    /**
     * Handle the Source "restored" event.
     */
    public function restored(Source $source): void
    {
        // If we restore the source, we restore the chunks.
        KnowledgeChunk::withTrashed()
            ->where('source_id', $source->id)
            ->restore();
    }

    /**
     * Handle the Source "force deleted" event.
     */
    public function forceDeleted(Source $source): void
    {
        // If source is permanently gone, chunks should be too.
        KnowledgeChunk::withTrashed()
            ->where('source_id', $source->id)
            ->forceDelete();
    }
}
