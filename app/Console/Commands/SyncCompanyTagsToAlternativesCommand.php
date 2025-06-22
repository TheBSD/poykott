<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;

class SyncCompanyTagsToAlternativesCommand extends Command
{
    protected $signature = 'tags:sync-company-to-alternatives';

    protected $description = "Sync each company's tags with its related alternative companies";

    public function handle(): int
    {
        $companies = Company::query()
            ->with(['tagsRelation', 'alternatives:id,name'])
            ->select('id', 'name')
            ->get();

        $this->output->progressStart(count($companies));

        foreach ($companies as $company) {
            $tagIds = $company->tagsRelation()->pluck('tag_id')->toArray();

            if (empty($tagIds)) {
                continue;
            }

            foreach ($company->alternatives as $alternative) {
                $alternative->tagsRelation()->syncWithoutDetaching($tagIds);
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        return Command::SUCCESS;
    }
}
