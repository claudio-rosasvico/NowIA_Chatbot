<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TenantVerificationSeeder extends Seeder
{
    public function run(): void
    {
        // Tenant A
        $uA = User::where('email', 'tenant_a@test.com')->first();
        if ($uA) {
            $uA->is_active = true;
            $uA->save();
            $orgA = $uA->currentOrganization();

            if (!$orgA->sources()->where('title', 'Secret Source A')->exists()) {
                $orgA->sources()->create([
                    'type' => 'text',
                    'title' => 'Secret Source A',
                    'text_content' => 'Confidential info for Org A',
                    'status' => 'active',
                    'meta' => []
                ]);
            }
            if (!$orgA->bots()->where('name', 'Bot A')->exists()) {
                $orgA->bots()->create([
                    'name' => 'Bot A',
                    'channel' => 'web',
                    'config' => []
                ]);
            }
        }

        // Tenant B
        $uB = User::where('email', 'tenant_b@test.com')->first();
        if ($uB) {
            $uB->is_active = true;
            $uB->save();
        }
    }
}
