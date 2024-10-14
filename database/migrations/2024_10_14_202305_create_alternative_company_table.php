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

        Schema::create('alternative_company', function (Blueprint $table) {
            $table->foreignId('alternative_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->primary(['alternative_id', 'company_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alternative_company');
    }
};
