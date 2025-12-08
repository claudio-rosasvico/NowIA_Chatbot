<?php

use App\Models\Source;
use App\Models\KnowledgeChunk;
use App\Services\Chunker;
use App\Services\PdfUrlExtractor;

$sourceId = 39; // ID reportado por el usuario
$source = Source::find($sourceId);

if (!$source) {
    echo "Source {$sourceId} not found.\n";
    // buscar el último
    $source = Source::latest()->first();
    if (!$source) {
        echo "No sources found at all.\n";
        exit;
    }
    echo "Using last source found: ID {$source->id} (Type: {$source->type})\n";
}

echo "Source ID: {$source->id}\n";
echo "Organization ID: {$source->organization_id}\n";

// 1. Simulate Extraction
$pdf = new PdfUrlExtractor();
$text = '';
$meta = ['title' => $source->title];

try {
    if ($source->type === 'text') {
        $text = $source->text_content;
    } elseif ($source->type === 'pdf') {
        echo "Extracting PDF from: {$source->storage_path}\n";
        $res = $pdf->extract($source->storage_path);
        $text = $res['text'];
    } elseif ($source->type === 'url') {
        echo "Extracting URL: {$source->url}\n";
        // Simple logic for debug
        $text = "Dummy text for URL debug";
    }
} catch (\Exception $e) {
    echo "Extraction Error: " . $e->getMessage() . "\n";
    exit;
}

echo "Extracted Text Length: " . strlen($text) . "\n";

if (trim($text) === '') {
    echo "Text is empty!\n";
    exit;
}

// 2. Simulate Chunking
$chunker = new Chunker();
$pieces = $chunker->make($text, 900, 120);

echo "Chunks created: " . count($pieces) . "\n";

if (empty($pieces)) {
    echo "Chunker returned empty array.\n";
    exit;
}

// 3. Simulate DB Insert
echo "Inserting chunks...\n";
$ids = [];
foreach ($pieces as $i => $c) {
    try {
        $kc = KnowledgeChunk::create([
            'organization_id' => $source->organization_id,
            'source_id' => $source->id,
            'position' => $i + 1,
            'content' => $c,
            'metadata' => $meta,
        ]);
        $ids[] = $kc->id;
        echo "Created Chunk ID: {$kc->id}\n";
    } catch (\Exception $e) {
        echo "DB Insert Error: " . $e->getMessage() . "\n";
    }
}

echo "Total inserted: " . count($ids) . "\n";

// 4. Verify Visibility (Global Scope)
echo "Verifying visibility for User ID " . auth()->id() . "...\n";
$count = KnowledgeChunk::where('source_id', $source->id)->count();
echo "Count via Eloquent (Scope applied?): {$count}\n";

$rawCount = \Illuminate\Support\Facades\DB::table('knowledge_chunks')->where('source_id', $source->id)->count();
echo "Count via DB Query (Raw): {$rawCount}\n";
