<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('similar_sites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url')->nullable()->unique();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('similar_sites')->nullOnDelete();
            $table->timestamps();

            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('similar_sites');
    }
};
