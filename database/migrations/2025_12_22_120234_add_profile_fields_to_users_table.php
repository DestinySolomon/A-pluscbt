<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->text('bio')->nullable()->after('phone');
            $table->string('profile_image')->nullable()->after('bio');
            $table->string('facebook_url')->nullable()->after('profile_image');
            $table->string('twitter_url')->nullable()->after('facebook_url');
            $table->string('linkedin_url')->nullable()->after('twitter_url');
            $table->string('instagram_url')->nullable()->after('linkedin_url');
            $table->boolean('email_notifications')->default(true)->after('instagram_url');
            $table->boolean('exam_notifications')->default(true)->after('email_notifications');
            $table->boolean('result_notifications')->default(true)->after('exam_notifications');
            $table->boolean('system_notifications')->default(true)->after('result_notifications');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'bio',
                'profile_image',
                'facebook_url',
                'twitter_url',
                'linkedin_url',
                'instagram_url',
                'email_notifications',
                'exam_notifications',
                'result_notifications',
                'system_notifications'
            ]);
        });
    }
};