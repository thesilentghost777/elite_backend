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
            PackSeeder::class,
            DigitalCourseSeeder::class,
            ModuleQuizSeeder::class,
            ProfilePackSeeder::class,
            RoadmapSeeder::class,
            BibliothequeSeeder::class,
        ]);
    }
}
