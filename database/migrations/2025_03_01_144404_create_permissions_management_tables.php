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
        Schema::create('roles', function (Blueprint $table) {
            // الأدوار
            $table->id(); // معرف الدور
            $table->string('name')->unique(); // اسم الدور
            $table->enum('type', ['مشرف', 'معلم', 'طالب'])->default('مشرف'); // نوع الصلاحية (مشرف، معلم، طالب)
            $table->text('description')->nullable(); // الوصف
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            // الصلاحيات
            $table->id(); // معرف الصلاحية
            $table->foreignId('role_id')->constrained('roles'); // معرف الدور
            $table->string('name'); // اسم الصلاحية
            $table->text('description')->nullable(); // الوصف
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            // الطلاب
            $table->id(); // معرف الطالب
            $table->string('card')->unique();
            $table->string('name'); // الاسم الكامل
            $table->enum('gender', ['ذكر', 'انثى'])->nullable(); // الجنس
            $table->string('address')->nullable(); // العنوان
            $table->date('birth_date')->nullable(); // تاريخ الميلاد
            $table->integer('enrollment_year')->nullable(); // سنة الالتحاق
            $table->foreignId('department_id')->constrained('departments'); // معرف القسم
            $table->enum('level', allowed: ['المستوى الاول', 'المستوى الثاني', 'المستوى الثالث', 'المستوى الرابع', 'المستوى الخامس', 'المستوى السادس', 'المستوى السابع'])->nullable(); // اليوم
            $table->string('active_code');
            $table->enum('qualification', ['الدبلوم', 'البكالوريوس', 'الماجستير', 'الدكتوراه']);
            $table->boolean('is_used')->default(0);
            $table->dateTime('timeout')->nullable();
            $table->boolean('is_login')->default(0);
            $table->timestamps();
        });

        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('gender', ['ذكر', 'انثى'])->nullable();
            $table->foreignId('college_id')->constrained('colleges')->onDelete('cascade'); // غير قابل للقيمة الفارغة
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade'); // غير قابل للقيمة الفارغة
            $table->enum('academic_degree', ['أستاذ دكتور', 'أستاذ مشارك', 'أستاذ مساعد', 'مدرس', 'معيد']);
            $table->string('specialization')->nullable(); // المستوى الأكاديمي
            $table->boolean('is_used')->default(0);
            $table->boolean('is_login')->default(0);
            $table->dateTime('timeout')->nullable();
            $table->timestamps();
        });
        
        Schema::create('users', function (Blueprint $table) {
            // المستخدمون
            $table->id(); // معرف المستخدم
            $table->string('code')->unique(); // اسم المستخدم
            $table->string('name'); // الاسم
            $table->string('password'); // كلمة المرور
            $table->string('email')->unique(); // البريد الإلكتروني
            $table->string('image_url')->nullable(); // رابط الصورة
            $table->dateTime('last_login')->nullable(); // آخر تسجيل دخول
            $table->dateTime('last_logout')->nullable(); // آخر تسجيل دخول
            $table->enum('status', ['متصل', 'غير متصل'])->default('متصل'); // الحالة
            $table->foreignId('role_id')->constrained('roles'); // معرف الدور
            $table->string('phone_number')->nullable(); // رقم الهاتف
            $table->enum('gender', ['ذكر', 'انثى'])->nullable();
            $table->enum('user', ['مشرف', 'معلم', 'طالب'])->default('مشرف'); // الحالة
            $table->integer('user_id')->nullable(); // معرف المستخدم
            $table->boolean('is_active')->default(1); // معرف المستخدم
            $table->timestamps();
        });
        
        Schema::create('contact_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('platform', ['phone', 'email', 'telegram', 'whatsapp', 'facebook', 'twitter', 'instagram', 'linkedin', 'tiktok', 'youtube', 'snapchat', 'website', 'other']);
            $table->string('url');
            $table->timestamps();
        });
        

        Schema::create('students_data', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // الاسم
            $table->json('data'); // البيانات (من نوع JSON)
            $table->boolean('status')->default(true); // الحالة (من نوع boolean مع قيمة افتراضية true)
            $table->timestamps(); // سيضيف created_at و updated_at تلقائيًا
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('students');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('users');
        Schema::dropIfExists('contact_infos');
        Schema::dropIfExists('students_data');
    }
};
