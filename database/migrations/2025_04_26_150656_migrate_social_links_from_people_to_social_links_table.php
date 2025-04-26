<?php

use App\Console\Commands\MigrateSocialLinksFromPeopleCommand;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Artisan::call(MigrateSocialLinksFromPeopleCommand::class);

        if (Schema::hasColumn('people', 'social_links')) {
            Schema::table('people', function (Blueprint $table): void {
                $table->dropColumn('social_links');
            });
        }
    }

    public function down(): void
    {
        Schema::table('people', function (Blueprint $table): void {
            $table->json('social_links')->nullable();
        });
    }
};
