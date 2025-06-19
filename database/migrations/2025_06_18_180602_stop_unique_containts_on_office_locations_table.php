<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('office_locations', function (Blueprint $table): void {
            $table->dropUnique(['name']);
        });
    }

    public function down(): void
    {
        Schema::table('office_locations', function (Blueprint $table): void {
            $table->string('name')->unique()->change();
        });
    }
};
