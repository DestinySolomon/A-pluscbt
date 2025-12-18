<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Question;

class TestExamSeeder extends Seeder
{
    public function run()
    {
        // Create or get English subject
        $english = Subject::firstOrCreate(
            ['code' => 'ENG'],
            [
                'name' => 'English Language',
                'code' => 'ENG',
                'description' => 'English Language and Literature',
                'is_jamb_subject' => true,
                'icon_class' => 'ri-book-line',
                'is_active' => true
            ]
        );

        // Create or get other subjects
        $subjectsData = [
            ['name' => 'Mathematics', 'code' => 'MTH', 'description' => 'Mathematics and Statistics', 'is_jamb_subject' => true, 'icon_class' => 'ri-calculator-line'],
            ['name' => 'Physics', 'code' => 'PHY', 'description' => 'Physics and Physical Sciences', 'is_jamb_subject' => true, 'icon_class' => 'ri-atom-line'],
            ['name' => 'Chemistry', 'code' => 'CHM', 'description' => 'Chemistry and Chemical Sciences', 'is_jamb_subject' => true, 'icon_class' => 'ri-flask-line'],
            ['name' => 'Biology', 'code' => 'BIO', 'description' => 'Biology and Life Sciences', 'is_jamb_subject' => true, 'icon_class' => 'ri-microscope-line'],
        ];

        $subjects = [];
        foreach ($subjectsData as $data) {
            $subjects[$data['code']] = Subject::firstOrCreate(
                ['code' => $data['code']],
                $data
            );
        }

        // Create exam
        $exam = Exam::create([
            'name' => 'JAMB UTME Mock Exam 2024',
            'code' => 'JAMB-MOCK-2024-001',
            'description' => 'Full JAMB mock examination with 180 questions covering English and 3 other subjects. This is a test exam to verify the exam management system.',
            'type' => 'full_jamb',
            'duration_minutes' => 120,
            'total_questions' => 180,
            'passing_score' => 50,
            'max_attempts' => 3,
            'shuffle_questions' => true,
            'shuffle_options' => true,
            'show_results_immediately' => true,
            'is_active' => true,
            'is_published' => true, // Set as published for testing
        ]);

        // Attach subjects
        $exam->subjects()->attach($english->id, [
            'question_count' => 60,
            'difficulty_distribution' => json_encode(['easy' => 30, 'medium' => 50, 'hard' => 20])
        ]);

        $exam->subjects()->attach($subjects['MTH']->id, [
            'question_count' => 40,
            'difficulty_distribution' => json_encode(['easy' => 30, 'medium' => 50, 'hard' => 20])
        ]);

        $exam->subjects()->attach($subjects['PHY']->id, [
            'question_count' => 40,
            'difficulty_distribution' => json_encode(['easy' => 30, 'medium' => 50, 'hard' => 20])
        ]);

        $exam->subjects()->attach($subjects['CHM']->id, [
            'question_count' => 40,
            'difficulty_distribution' => json_encode(['easy' => 30, 'medium' => 50, 'hard' => 20])
        ]);

        // Create sample questions for each subject
        $this->createSampleQuestions($english, 10);
        $this->createSampleQuestions($subjects['MTH'], 10);
        $this->createSampleQuestions($subjects['PHY'], 10);
        $this->createSampleQuestions($subjects['CHM'], 10);
        
        echo "Test exam created successfully!\n";
        echo "Exam ID: {$exam->id}\n";
        echo "Exam Name: {$exam->name}\n";
        echo "Access at: /admin/exams/{$exam->id}\n";
    }

    private function createSampleQuestions($subject, $count = 10)
    {
        $questions = [];
        
        for ($i = 1; $i <= $count; $i++) {
            $questions[] = [
                'subject_id' => $subject->id,
                'question' => "Sample question {$i} for {$subject->name}: What is the correct answer?",
                'option_a' => 'Option A',
                'option_b' => 'Option B',
                'option_c' => 'Option C',
                'option_d' => 'Option D',
                'correct_option' => 'b',
                'explanation' => 'This is a sample question for testing purposes.',
                'difficulty' => $i <= 3 ? 'easy' : ($i <= 7 ? 'medium' : 'hard'),
                'is_active' => true
            ];
        }
        
        Question::insert($questions);
    }
}