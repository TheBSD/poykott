<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temp_media', function (Blueprint $table): void {
            $table->id();
            $table->morphs('mediable');
            $table->string('url');
            $table->string('collection_name')->default('default');
            $table->string('disk')->default('public');
            $table->boolean('is_processed')->default(false);
            $table->json('meta')->nullable();
            $table->timestamps();

            // Index for faster lookups
            $table->index(['mediable_type', 'mediable_id', 'collection_name']);
            $table->index('is_processed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temp_media');
    }
};
