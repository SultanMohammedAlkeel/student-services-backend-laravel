<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // جدول الاشعارات
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->boolean('is_delivered')->default(false);
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('hall_id')->constrained('halls')->onDelete('cascade');
            $table->enum('level', ['المستوى الاول', 'المستوى الثاني', 'المستوى الثالث', 'المستوى الرابع', 'المستوى الخامس', 'المستوى السادس', 'المستوى السابع'])->nullable(); // اليوم
            $table->enum('period', ['08:00 - 10:00', '10:00 - 12:00', '12:00 - 02:00']);
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
            $table->timestamps();
        });

        // جدول الرد على الاشعارات
        Schema::create('notification_replies', function (Blueprint $table) {
            $table->id();
            $table->boolean('confirmation');
            $table->text('content')->nullable();
            $table->foreignId('notification_id')->constrained('notifications')->onDelete('cascade');
            $table->timestamps();
        });

        // جدول حجز القاعات
        Schema::create('hall_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hall_id')->constrained('halls')->onDelete('cascade');
            $table->enum('period', ['08:00 - 10:00', '10:00 - 12:00', '12:00 - 02:00']);
            $table->enum('level', ['المستوى الاول', 'المستوى الثاني', 'المستوى الثالث', 'المستوى الرابع', 'المستوى الخامس', 'المستوى السادس', 'المستوى السابع'])->nullable(); 
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('schedule_id')->nullable()->constrained('schedules')->onDelete('cascade');
            $table->timestamps();
        });

        // جدول سجل حضور الطلاب
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->enum('period', ['08:00 - 10:00', '10:00 - 12:00', '12:00 - 02:00']);
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->enum('level', ['المستوى الاول', 'المستوى الثاني', 'المستوى الثالث', 'المستوى الرابع', 'المستوى الخامس', 'المستوى السادس', 'المستوى السابع'])->nullable(); 
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->date('lecture_date');
            $table->integer('lecture_number');  
            $table->json('data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('hall_bookings');
        Schema::dropIfExists('notification_replies');
        Schema::dropIfExists('notifications');
    }
};