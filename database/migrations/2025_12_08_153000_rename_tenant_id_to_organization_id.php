<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('knowledge_chunks', 'tenant_id') && !Schema::hasColumn('knowledge_chunks', 'organization_id')) {
            Schema::table('knowledge_chunks', function (Blueprint $table) {
                // Rename tenant_id to organization_id
                $table->renameColumn('tenant_id', 'organization_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('knowledge_chunks', 'organization_id')) {
            Schema::table('knowledge_chunks', function (Blueprint $table) {
                $table->renameColumn('organization_id', 'tenant_id');
            });
        }
    }
};
