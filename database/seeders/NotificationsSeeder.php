<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Notification;

class NotificationsSeeder extends Seeder
{
    public function run(): void
    {
       $admin = User::where('email', 'admin@apluscbt.com')->first();
        
        if ($admin) {
            // Create sample notifications for admin
            $notifications = [
                [
                    'type' => 'user',
                    'title' => 'New Student Registered',
                    'message' => 'John Doe has registered as a new student.',
                    'link' => '/admin/users',
                    'created_at' => now()->subMinutes(5),
                ],
                [
                    'type' => 'exam',
                    'title' => 'Exam Completed',
                    'message' => 'Student Jane Smith has completed the Mathematics exam.',
                    'link' => '/admin/results',
                    'created_at' => now()->subHours(1),
                ],
                [
                    'type' => 'question',
                    'title' => 'New Question Added',
                    'message' => 'A new question has been added to the Physics question bank.',
                    'link' => '/admin/questions',
                    'created_at' => now()->subHours(3),
                    'is_read' => true,
                    'read_at' => now()->subHours(2),
                ],
                [
                    'type' => 'system',
                    'title' => 'System Update Available',
                    'message' => 'A new system update is available. Please review the changelog.',
                    'link' => '/admin/settings',
                    'created_at' => now()->subDays(1),
                ],
                [
                    'type' => 'result',
                    'title' => 'Top Performer',
                    'message' => 'Student Alex Johnson scored 95% in the Chemistry exam.',
                    'link' => '/admin/results/top-performers',
                    'created_at' => now()->subDays(2),
                    'is_read' => true,
                    'read_at' => now()->subDays(1),
                ],
            ];
            
            foreach ($notifications as $notificationData) {
                Notification::create(array_merge($notificationData, [
                    'user_id' => $admin->id,
                ]));
            }
            
            $this->command->info('Sample notifications created for admin.');
        }
    }
}