<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("password_reset_codes", function (Blueprint $table) {
            $table->id();
            $table->string("email", 191)->index(); // تحديد الطول ليتوافق مع جدول users
            $table->string("code", 6)->index();
            $table->enum("type", ["password_reset", "email_verification"])->default("password_reset");
            $table->enum("status", ["pending", "used", "expired"])->default("pending")->index();
            $table->timestamp("expires_at")->index();
            $table->timestamp("used_at")->nullable();
            $table->integer("attempts")->default(0);
            $table->integer("max_attempts")->default(3);
            $table->string("ip_address", 45)->nullable();
            $table->text("user_agent")->nullable();
            $table->timestamps();

            $table->index(["email", "code", "status"], "email_code_status_index");
       //     $table->foreign("email")->references("email")->on("users")->onDelete("cascade");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("password_reset_codes");
    }
};


