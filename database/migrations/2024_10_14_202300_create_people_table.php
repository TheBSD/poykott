<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('job_title')->nullable();
            $table->string('url')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('approved_at')->nullable()->index();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->json('social_links')->nullable();
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
