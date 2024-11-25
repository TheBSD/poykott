<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('alternative_company', function (Blueprint $table): void {
            $table->foreignId('alternative_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->primary(['alternative_id', 'company_id']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('alternative_company');
    }
};
