<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Makan & Minum',  'icon' => '🍜', 'color' => '#f97316'],
            ['name' => 'Transport',      'icon' => '🚌', 'color' => '#3b82f6'],
            ['name' => 'Akademik',       'icon' => '📚', 'color' => '#8b5cf6'],
            ['name' => 'Hiburan',        'icon' => '🎮', 'color' => '#ec4899'],
            ['name' => 'Blind Box',      'icon' => '🎁', 'color' => '#f43f5e'],
            ['name' => 'Ngopi',          'icon' => '☕', 'color' => '#92400e'],
            ['name' => 'Kesehatan',      'icon' => '💊', 'color' => '#10b981'],
            ['name' => 'Kos/Kontrakan',  'icon' => '🏠', 'color' => '#6366f1'],
            ['name' => 'Kuota/Internet', 'icon' => '📱', 'color' => '#0ea5e9'],
            ['name' => 'Lainnya',        'icon' => '💸', 'color' => '#94a3b8'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name']],
                [...$cat, 'is_default' => true]
            );
        }
    }
}