<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $companies = DB::table('companies')->whereNotNull('exit_valuation')->get();

        foreach ($companies as $company) {
            DB::table('companies')
                ->where('id', $company->id)
                ->update([
                    'exit_valuation' => Str::ltrim($company->exit_valuation, '$'),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            //
        });
    }
};
