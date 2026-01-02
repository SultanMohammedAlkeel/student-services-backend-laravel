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
        Schema::create('universities', function (Blueprint $table) {
            // الجامعة
            $table->id(); // معرف الجامعة
            $table->string('name'); // اسم الجامعة
            $table->string('logo_url')->nullable(); // رابط شعار الجامعة
            $table->string('contact_info')->nullable(); // معلومات الاتصال
            $table->string('email')->nullable(); // البريد الإلكتروني
            $table->text('description')->nullable(); // الوصف
            $table->string('website')->nullable(); // الموقع الإلكتروني
            $table->timestamps();
        });
        
        Schema::create('colleges', function (Blueprint $table) {
            // الكليات
            $table->id(); // معرف الكلية
            $table->string('name'); // اسم الكلية
            $table->string('logo_url')->nullable(); // رابط شعار الكلية
            // $table->string('dean')->nullable(); // عميد الكلية
            $table->foreignId('university_id')->constrained('universities'); // معرف الجامعة
            $table->string('contact_info')->nullable(); // معلومات الاتصال
            $table->timestamps();
        });
        
        Schema::create('departments', function (Blueprint $table) {
            // الأقسام
            $table->id(); // معرف القسم
            $table->string('name'); // اسم القسم
            $table->string('short_name')->nullable(); // الاسم المختصر
            $table->foreignId('college_id')->constrained('colleges'); // معرف الكلية
            $table->integer('levels'); // عدد المستويات
            $table->text('description')->nullable(); // الوصف
            $table->timestamps();
        });
        
        Schema::create('buildings', function (Blueprint $table) {
            // المباني
            $table->id(); // معرف المبنى
            $table->string('name'); // اسم المبنى
            $table->string('location')->nullable(); // الموقع
            $table->text('description')->nullable(); // الوصف
            $table->timestamps();
        });


        Schema::create('colleges_buildings', function (Blueprint $table) {
            // المباني
            $table->id(); // معرف المبنى
            $table->foreignId('college_id')->constrained('colleges'); // معرف الكلية
            $table->foreignId('building_id')->constrained('buildings'); // معرف الكلية
            $table->timestamps();
        });
        
        Schema::create('halls', function (Blueprint $table) {
            // الفصول الدراسية
            $table->id(); // معرف الفصل
            $table->string('name'); // اسم الفصل
            $table->foreignId('building_id')->constrained('buildings'); // معرف المبنى
            $table->integer('capacity')->nullable(); // السعة
            $table->enum('type', ['محاضرات', 'معمل', 'ندوة'])->nullable(); // النوع
            $table->timestamps();
        });

        Schema::create('academic_years', function (Blueprint $table) {
            // الفصول الدراسية
            $table->id(); // معرف الفصل
            $table->string('start_date'); // اسم الفصل
            $table->string('end_date'); // اسم الفصل
            $table->boolean('status')->default(true); // الحالة (من نوع boolean مع قيمة افتراضية true)
            $table->timestamps();
        });

        Schema::create('periods', function (Blueprint $table) {
            // الفصول الدراسية
            $table->id(); // معرف الفصل
            $table->foreignId('academic_year_id')->constrained('academic_year'); 
            $table->enum('term', ['الاول', 'الثاني']); 
            $table->date('start_date'); 
            $table->date('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('universities');
        Schema::dropIfExists('colleges');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('buildings');
        Schema::dropIfExists('colleges_buildings');
        Schema::dropIfExists('halls');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('periods');
    }
};
