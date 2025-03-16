<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $totalRecords = 100000;
        $chunkSize = 10000; // Insert in batches of 10,000

        for ($i = 0; $i < $totalRecords; $i += $chunkSize) {
            \App\Models\Translation::withoutEvents(function () use ($chunkSize) {
                \App\Models\Translation::factory()->count($chunkSize)->create();
            });
        }
    }
}
