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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    
        Schema::create('books', function (Blueprint $table) {
            // موارد المكتبة
            $table->id(); // معرف المورد
            $table->string('code')->unique();
            $table->string('title'); // العنوان
            $table->string('author')->nullable(); // المؤلف
            $table->foreignId('added_by')->constrained('users'); // معرف المستخدم الذي أضاف المورد
            $table->enum('type', ['عام', 'خاص', 'مشترك','محدد']);
            $table->foreignId('category_id')->constrained('categories');  // التصنيف
            $table->text('description')->nullable(); // الوصف
            $table->string('file_url'); // رابط الملف
            $table->enum('file_type', ['PDF ', 'Microsoft Word', 'Microsoft Excel', 'PowerPoint', 'Text Files', 'Programming Files', 'Executable Files', 'Database Files']);
            $table->bigInteger('file_size');
            $table->foreignId('college_id')->nullable()->constrained('colleges')->onDelete('cascade'); // يقبل NULL
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('cascade'); // يقبل NULL
            $table->enum('level', ['المستوى الاول', 'المستوى الثاني', 'المستوى الثالث', 'المستوى الرابع', 'المستوى الخامس', 'المستوى السادس', 'المستوى السابع'])->nullable(); // اليوم
            $table->integer('opens_count')->default(0);
            $table->integer('download_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('save_count')->default(0);
            $table->boolean('is_active')->default(1); // حالة المورد
            $table->timestamps();
        });
    
        Schema::create('book_infos', function (Blueprint $table) {
            // موارد المكتبة
            $table->id(); // معرف المورد
            $table->foreignId('book_id')->constrained('books'); // معرف جدول المحاضرات
            $table->foreignId('user_id')->constrained('users');
            $table->boolean('save')->default(0);
            $table->integer('opens_count')->default(0);
            $table->boolean('likes')->default(0);
            $table->integer('downloads')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
        Schema::dropIfExists('books');
        Schema::dropIfExists('book_infos');
    }
};
