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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('avatar')->nullable();
            $table->string('slug')->unique();
            $table->string('job_title')->nullable();
            $table->string('url')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('location')->nullable();
            $table->text('biography')->nullable();
            $table->json('social_links')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
