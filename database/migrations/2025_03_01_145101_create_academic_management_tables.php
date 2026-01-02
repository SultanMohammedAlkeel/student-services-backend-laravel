<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /*
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            // الكورسات
            $table->id(); // معرف الكورس
            $table->string('name'); // اسم الكورس
            $table->enum('type', ['متطلب', 'مقرر', 'عام'])->nullable(); // 
            $table->foreignId('department_id')->constrained('departments'); // معرف القسم
            $table->enum('level', ['المستوى الاول', 'المستوى الثاني', 'المستوى الثالث', 'المستوى الرابع', 'المستوى الخامس', 'المستوى السادس', 'المستوى السابع'])->nullable(); // اليوم
            $table->enum('term', ['الاول', 'الثاني'])->nullable(); // اليوم
            $table->text('description')->nullable(); // الوصف
            $table->timestamps();
        });
        
        Schema::create('schedules', function (Blueprint $table) {
            // جدول المحاضرات
            $table->id(); // معرف الجدول
            $table->foreignId('department_id')->constrained('departments'); // معرف الكورس
            $table->enum('level', ['المستوى الاول', 'المستوى الثاني', 'المستوى الثالث', 'المستوى الرابع', 'المستوى الخامس', 'المستوى السادس', 'المستوى السابع'])->nullable(); // اليوم
            $table->enum('term', ['الاول', 'الثاني'])->nullable(); // اليوم
            $table->foreignId('academic_year_id')->constrained('academic_year'); // معرف الكورس
            $table->json('schedule')->nullable(); // الجدول
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
        Schema::dropIfExists('schedules');
    }
};
