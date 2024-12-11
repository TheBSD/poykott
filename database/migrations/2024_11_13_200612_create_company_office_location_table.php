<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_office_location', function (Blueprint $table): void {
            $table->foreignId('company_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('office_location_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->primary(['company_id', 'office_location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_office_location');
    }
};
