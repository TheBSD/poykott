<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('similar_sites', function (Blueprint $table): void {
            $table->string('url')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('similar_sites', function (Blueprint $table): void {
            $table->string('url')->nullable()->change();
        });
    }
};
