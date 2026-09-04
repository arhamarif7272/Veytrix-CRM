<?php

namespace Database\Seeders;

use App\Models\DealStage;
use Illuminate\Database\Seeder;

class DealStageSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing stages first (idempotent)
        DealStage::truncate();

        $stages = [
            ['name' => 'Prospecting',   'order' => 1, 'color' => '#6366f1', 'win_probability' => 10, 'is_won' => false, 'is_lost' => false, 'is_default' => true],
            ['name' => 'Qualification', 'order' => 2, 'color' => '#8b5cf6', 'win_probability' => 25, 'is_won' => false, 'is_lost' => false, 'is_default' => false],
            ['name' => 'Proposal',      'order' => 3, 'color' => '#0ea5e9', 'win_probability' => 50, 'is_won' => false, 'is_lost' => false, 'is_default' => false],
            ['name' => 'Negotiation',   'order' => 4, 'color' => '#f59e0b', 'win_probability' => 75, 'is_won' => false, 'is_lost' => false, 'is_default' => false],
            ['name' => 'Closed Won',    'order' => 5, 'color' => '#10b981', 'win_probability' => 100,'is_won' => true,  'is_lost' => false, 'is_default' => false],
            ['name' => 'Closed Lost',   'order' => 6, 'color' => '#ef4444', 'win_probability' => 0,  'is_won' => false, 'is_lost' => true,  'is_default' => false],
        ];

        foreach ($stages as $stage) {
            DealStage::create($stage);
        }

        $this->command->info('Deal stages seeded: ' . count($stages) . ' stages created.');
    }
}
