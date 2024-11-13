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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('exit_strategy_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('funding_level_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('company_size_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamp('approved_at')->nullable();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('url');
            $table->text('description')->nullable();
            $table->string('short_description')->nullable();
            $table->text('notes')->nullable();
            $table->integer('valuation')->nullable();
            $table->integer('exit_valuation')->nullable();
            $table->string('stock_symbol')->nullable();
            $table->integer('total_funding')->nullable();
            $table->date('last_funding_date')->nullable();
            $table->string('headquarter')->nullable();
            $table->date('founded_at')->nullable();
            $table->json('office_locations')->nullable();
            $table->integer('employee_count')->nullable();
            $table->string('stock_quote')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
