<?php

namespace Database\Seeders;

use App\Models\ProjectType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Internship', 'slug' => 'internship'],
            ['name' => 'Bachelor Thesis Project', 'slug' => 'bachelor_thesis'],
            ['name' => 'Master Thesis Project', 'slug' => 'master_thesis'],
        ];

        foreach ($types as $type) {
            ProjectType::firstOrCreate(
                ['slug' => $type['slug']],
                ['name' => $type['name']]
            );
        }
    }
}
