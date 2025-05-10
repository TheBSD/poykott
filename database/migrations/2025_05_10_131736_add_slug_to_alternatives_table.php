<?php

use App\Models\Alternative;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * Add nullable slug column
         */
        if (! Schema::hasColumn('alternatives', 'slug')) {
            Schema::table('alternatives', function (Blueprint $table): void {
                $table->string('slug')->nullable()->after('name');
            });
        }

        /**
         * Generate slugs for existing records
         */
        Alternative::all()->each(function (Alternative $alternative): void {
            /**
             *Spatie Sluggable generate slugs implicitly when saving the model.
             */
            $alternative->touch();
        });

        Schema::table('alternatives', function (Blueprint $table): void {
            $table->string('slug')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('alternatives', function (Blueprint $table): void {
            $table->dropColumn('slug');
        });
    }
};
