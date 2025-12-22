<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Notification;
use App\Models\User;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition()
    {
        $types = ['exam', 'result', 'system', 'user', 'question'];
        $type = $this->faker->randomElement($types);
        
        $titles = [
            'exam' => ['New Exam Created', 'Exam Updated', 'Exam Starting Soon'],
            'result' => ['Exam Results Published', 'New Certificate Available'],
            'system' => ['System Maintenance', 'New Feature Available', 'Important Update'],
            'user' => ['New Student Registered', 'User Account Activated'],
            'question' => ['New Question Added', 'Question Reported'],
        ];
        
        $messages = [
            'exam' => 'A new exam has been created and is now available for students.',
            'result' => 'Your exam results have been published. Check your performance.',
            'system' => 'System maintenance scheduled for this weekend.',
            'user' => 'A new student has registered on the platform.',
            'question' => 'A new question has been added to the question bank.',
        ];
        
        return [
            'user_id' => User::factory(),
            'type' => $type,
            'title' => $this->faker->randomElement($titles[$type]),
            'message' => $messages[$type],
            'data' => null,
            'link' => $this->faker->url(),
            'is_read' => $this->faker->boolean(30), // 30% chance of being read
            'read_at' => $this->faker->optional(0.3)->dateTimeThisMonth(),
            'created_at' => $this->faker->dateTimeThisMonth(),
        ];
    }
}