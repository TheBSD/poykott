<?php

use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add new string column if it doesn't exist
        Schema::table('companies', function (Blueprint $table): void {
            if (! Schema::hasColumn('companies', 'exit_strategy')) {
                $table->string('exit_strategy')->nullable()->after('exit_valuation');
            }
        });

        // 2. Copy data from relationship
        if (Schema::hasColumn('companies', 'exit_strategy_id')) {
            $companies = Company::with('exitStrategy')->get();

            foreach ($companies as $company) {
                $company->update([
                    'exit_strategy' => $company->exitStrategy?->title,
                ]);
            }
        }

        // 3. Drop foreign key constraint and column in SQLite-safe way
        if (Schema::hasColumn('companies', 'exit_strategy_id')) {
            // SQLite doesn't support dropping a foreign key directly
            // So we rebuild the table without the column
            Schema::table('companies', function (Blueprint $table): void {
                $table->dropForeign(['exit_strategy_id']);
            });

            // Laravel will now drop the column (this works if foreign key was removed first)
            Schema::table('companies', function (Blueprint $table): void {
                $table->dropColumn('exit_strategy_id');
            });
        }

        // 4. Drop the exit_strategies table
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
