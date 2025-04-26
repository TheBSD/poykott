<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_links', function (Blueprint $table): void {
            $table->id();
            $table->morphs('linkable');
            $table->string('url');
            $table->timestamps();

            $table->unique(['linkable_type', 'linkable_id', 'url'], 'unique_social_link_per_model');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_links');
    }
};
