<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->string('email')->after('slug');
            $table->string('personal_email')
                ->after('email')
                ->nullable();
            $table->string('icon_url')
                ->after('url')
                ->nullable();
            $table->string('tags')
                ->after('notes')
                ->nullable();
            $table->string('office_locations')
                ->after('tags')
                ->nullable();
            $table->string('resources')
                ->after('office_locations')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->dropColumn([
                'email',
                'personal_email',
                'icon_url',
                'tags',
                'office_locations',
                'resources',
            ]);
        });
    }
};
