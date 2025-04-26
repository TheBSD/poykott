<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // add exit_strategy column
        Schema::table('companies', function (Blueprint $table): void {
            if (! Schema::hasColumn('companies', 'exit_strategy')) {
                $table->string('exit_strategy')->nullable()->after('exit_valuation');
            }
        });

        // drop exit_strategy_id column and index
        if (Schema::hasColumn('companies', 'exit_strategy_id')) {
            Schema::table('companies', function (Blueprint $table): void {
                $table->dropForeign(['exit_strategy_id']);
            });

            Schema::table('companies', function (Blueprint $table): void {
                $table->dropColumn('exit_strategy_id');
            });
        }

        // drop exit_strategies table
        if (Schema::hasTable('exit_strategies')) {
            Schema::drop('exit_strategies');
        }
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->dropColumn('exit_strategy');
            $table->foreignId('exit_strategy_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::create('exit_strategies', function (Blueprint $table): void {
            $table->id();
            $table->string('title')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
};
