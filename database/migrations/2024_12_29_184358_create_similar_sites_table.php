<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('similar_sites', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('similar_site_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('url')->nullable()->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('similar_sites');
    }
};
