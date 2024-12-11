<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_investor', function (Blueprint $table): void {
            $table->foreignId('company_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('investor_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->primary(['company_id', 'investor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_investor');
    }
};
