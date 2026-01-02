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
        Schema::create('contacts', function (Blueprint $table) {
            // المحادثات
            $table->id(); // معرف المحادثة
            $table->foreignId('user_id')->constrained('users'); // معرف المستخدم الأول
            $table->foreignId('contact_id')->constrained('users'); // معرف المستخدم الثاني
            $table->enum('type', ['اداري', 'معلم', 'طالب']); // الحالة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
