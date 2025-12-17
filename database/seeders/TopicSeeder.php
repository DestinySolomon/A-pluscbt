<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Subject;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = Subject::all();
        
        foreach ($subjects as $subject) {
            $topics = $this->getTopicsForSubject($subject->code);
            
            foreach ($topics as $topic) {
                DB::table('topics')->insert([
                    'subject_id' => $subject->id,
                    'name' => $topic['name'],
                    'description' => $topic['description'],
                    'syllabus_ref' => $topic['syllabus_ref'],
                    'syllabus_order' => $topic['order'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        $this->command->info('Topics seeded successfully!');
    }
    
    private function getTopicsForSubject($subjectCode)
    {
        $topics = [
            'ENG' => [
                ['name' => 'Comprehension', 'description' => 'Reading and understanding passages', 'syllabus_ref' => 'ENG 1.1', 'order' => 1],
                ['name' => 'Lexis and Structure', 'description' => 'Vocabulary and sentence structure', 'syllabus_ref' => 'ENG 1.2', 'order' => 2],
                ['name' => 'Oral English', 'description' => 'Phonetics and speech', 'syllabus_ref' => 'ENG 1.3', 'order' => 3],
            ],
            'MAT' => [
                ['name' => 'Number Bases', 'description' => 'Binary, decimal, octal systems', 'syllabus_ref' => 'MAT 1.1', 'order' => 1],
                ['name' => 'Algebra', 'description' => 'Equations and expressions', 'syllabus_ref' => 'MAT 1.2', 'order' => 2],
                ['name' => 'Geometry', 'description' => 'Shapes and angles', 'syllabus_ref' => 'MAT 1.3', 'order' => 3],
                ['name' => 'Trigonometry', 'description' => 'Triangles and ratios', 'syllabus_ref' => 'MAT 1.4', 'order' => 4],
            ],
            'PHY' => [
                ['name' => 'Mechanics', 'description' => 'Motion and forces', 'syllabus_ref' => 'PHY 1.1', 'order' => 1],
                ['name' => 'Waves', 'description' => 'Sound and light waves', 'syllabus_ref' => 'PHY 1.2', 'order' => 2],
                ['name' => 'Electricity', 'description' => 'Circuits and magnetism', 'syllabus_ref' => 'PHY 1.3', 'order' => 3],
            ],
            'CHE' => [
                ['name' => 'Organic Chemistry', 'description' => 'Carbon compounds', 'syllabus_ref' => 'CHE 1.1', 'order' => 1],
                ['name' => 'Inorganic Chemistry', 'description' => 'Elements and compounds', 'syllabus_ref' => 'CHE 1.2', 'order' => 2],
                ['name' => 'Physical Chemistry', 'description' => 'Reactions and energy', 'syllabus_ref' => 'CHE 1.3', 'order' => 3],
            ],
            'BIO' => [
                ['name' => 'Plant Biology', 'description' => 'Plants structure and function', 'syllabus_ref' => 'BIO 1.1', 'order' => 1],
                ['name' => 'Animal Biology', 'description' => 'Animals structure and function', 'syllabus_ref' => 'BIO 1.2', 'order' => 2],
                ['name' => 'Genetics', 'description' => 'Heredity and variation', 'syllabus_ref' => 'BIO 1.3', 'order' => 3],
            ],
            'ECO' => [
                ['name' => 'Microeconomics', 'description' => 'Individual economic behavior', 'syllabus_ref' => 'ECO 1.1', 'order' => 1],
                ['name' => 'Macroeconomics', 'description' => 'National economy', 'syllabus_ref' => 'ECO 1.2', 'order' => 2],
            ],
            'GOV' => [
                ['name' => 'Political Systems', 'description' => 'Types of government', 'syllabus_ref' => 'GOV 1.1', 'order' => 1],
                ['name' => 'Constitutions', 'description' => 'National constitutions', 'syllabus_ref' => 'GOV 1.2', 'order' => 2],
            ],
            'LIT' => [
                ['name' => 'Prose', 'description' => 'Novels and short stories', 'syllabus_ref' => 'LIT 1.1', 'order' => 1],
                ['name' => 'Poetry', 'description' => 'Poems and verses', 'syllabus_ref' => 'LIT 1.2', 'order' => 2],
                ['name' => 'Drama', 'description' => 'Plays and theater', 'syllabus_ref' => 'LIT 1.3', 'order' => 3],
            ],
        ];
        
        return $topics[$subjectCode] ?? [];
    }
}