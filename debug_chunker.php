<?php

use App\Services\Chunker;

$chunker = new Chunker();

// Test 1: Clean text
$text = "Hola mundo.\n\nEste es un párrafo.";
$chunks = $chunker->make($text);
echo "Clean Text: " . count($chunks) . " chunks\n";

// Test 2: Invalid UTF-8
// Helper to create invalid UTF-8 string
$invalid = "Hola \xC3\x28 mundo"; // Invalid sequence
$chunks = $chunker->make($invalid);
echo "Invalid UTF-8: " . count($chunks) . " chunks (Expect 0 or failure)\n";
