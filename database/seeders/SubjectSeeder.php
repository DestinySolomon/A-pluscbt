<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            [
                'name' => 'English Language',
                'code' => 'ENG',
                'description' => 'JAMB English Language including comprehension, lexis and structure',
                'icon_class' => 'ri-english-input',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Mathematics',
                'code' => 'MAT',
                'description' => 'JAMB Mathematics covering algebra, geometry, calculus, etc.',
                'icon_class' => 'ri-calculator-line',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Physics',
                'code' => 'PHY',
                'description' => 'JAMB Physics covering mechanics, waves, electricity, etc.',
                'icon_class' => 'ri-flask-line',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Chemistry',
                'code' => 'CHE',
                'description' => 'JAMB Chemistry covering organic, inorganic, physical chemistry',
                'icon_class' => 'ri-test-tube-line',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Biology',
                'code' => 'BIO',
                'description' => 'JAMB Biology covering plant and animal biology, genetics, ecology',
                'icon_class' => 'ri-microscope-line',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Economics',
                'code' => 'ECO',
                'description' => 'JAMB Economics covering micro and macro economics',
                'icon_class' => 'ri-line-chart-line',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Government',
                'code' => 'GOV',
                'description' => 'JAMB Government covering political systems, constitutions',
                'icon_class' => 'ri-government-line',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Literature in English',
                'code' => 'LIT',
                'description' => 'JAMB Literature including prose, poetry, drama',
                'icon_class' => 'ri-book-2-line',
                'order' => 8,
                'is_active' => true,
            ],
        ];

        DB::table('subjects')->insert($subjects);
    }
}