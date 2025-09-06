<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Ingredient::factory()->createMany([
            ['name' => 'Flour'],
            ['name' => 'Sugar'],
            ['name' => 'Salt'],
            ['name' => 'Butter'],
            ['name' => 'Eggs'],
            ['name' => 'Milk'],
            ['name' => 'Baking Powder'],
            ['name' => 'Vanilla Extract'],
        ]);
    }
}
