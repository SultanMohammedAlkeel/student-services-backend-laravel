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
        // جدول الاختبارات
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('language', ['عربي', 'انجليزي']);
            $table->enum('type', ['اختيارات', 'صح و خطأ']);
            $table->foreignId('department_id')->constrained('departments');
            $table->enum('level', [
                'المستوى الاول', 
                'المستوى الثاني', 
                'المستوى الثالث', 
                'المستوى الرابع', 
                'المستوى الخامس', 
                'المستوى السادس', 
                'المستوى السابع'
            ]);
            $table->foreignId('created_by')->constrained('users');
            $table->json('exam_data');
            $table->timestamps();
        });

        // جدول سجل الاختبارات
        Schema::create('exam_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams');
            $table->foreignId('student_id')->constrained('users');
            $table->decimal('score', 5, 2);
            $table->integer('correct')->nullable();
            $table->integer('wrong')->nullable();
            $table->json('answers');
            $table->timestamps();
        });

        // جدول درجات الطلاب
        Schema::create('student_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers');
            $table->foreignId('course_id')->constrained('courses');
            $table->text('notes')->nullable();
            $table->foreignId('department_id')->constrained('departments');
            $table->enum('level', [
                'المستوى الاول', 
                'المستوى الثاني', 
                'المستوى الثالث', 
                'المستوى الرابع', 
                'المستوى الخامس', 
                'المستوى السادس', 
                'المستوى السابع'
            ]);
            $table->json('grades');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_grades');
        Schema::dropIfExists('exam_records');
        Schema::dropIfExists('exams');
    }
};