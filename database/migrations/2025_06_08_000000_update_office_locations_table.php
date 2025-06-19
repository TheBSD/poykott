<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOfficeLocationsTable extends Migration
{
    public function up(): void
    {
        Schema::table('office_locations', function (Blueprint $table): void {
            if (! Schema::hasColumn('office_locations', 'old_name')) {
                $table->string('old_name')->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('office_locations', function (Blueprint $table): void {
            $table->dropColumn('old_name');
        });
    }
}
