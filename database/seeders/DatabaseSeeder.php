<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CorrespondenceSeeder::class,
            CareerProfileSeeder::class,
            ProfileMatchingSeeder::class,
            ProfilePackSeeder::class,
            RoadmapSeeder::class,
            PackSeeder::class,
        ]);
    }
}
