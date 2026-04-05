<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->bigInteger('valuation')->nullable()->change();
            $table->bigInteger('exit_valuation')->nullable()->change();
            $table->bigInteger('total_funding')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->integer('valuation')->nullable()->change();
            $table->integer('exit_valuation')->nullable()->change();
            $table->integer('total_funding')->nullable()->change();
        });
    }
};
