<?php

namespace App\Console\Commands;

use App\Models\Alternative;
use App\Models\Company;
use App\Models\Investor;
use App\Models\Person;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate the sitemap';

    public function handle(): void
    {
        $this->info('Generating sitemap...');

        $sitemap = Sitemap::create()
            ->add(Url::create('/'))
            ->add(Url::create('/companies'))
            ->add(Url::create('/about'))
            ->add(Url::create('/faqs'))
            ->add(Url::create('/contact'))
            ->add(Url::create('/newsletter'))
            ->add(Url::create('/similar-sites'));

        // Add all alternatives
        $this->info('Adding alternatives to sitemap...');
        Alternative::query()
            ->approved()
            ->select('id', 'slug', 'updated_at')
            ->get()
            ->each(function (Alternative $alternative) use ($sitemap): void {
                $sitemap->add(
                    Url::create("/alternative/{$alternative->slug}")
                        ->setLastModificationDate($alternative->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.8)
                );
            });

        // Add companies
        $this->info('Adding companies to sitemap...');
        Company::query()
            ->approved()
            ->select('id', 'slug', 'updated_at')
            ->get()
            ->each(function (Company $company) use ($sitemap): void {
                $sitemap->add(
                    Url::create("/companies/{$company->slug}")
                        ->setLastModificationDate($company->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.8)
                );
            });

        // Add people
        $this->info('Adding people to sitemap...');
        Person::query()
            ->whereHas('companies', fn (Builder $query) => $query->approved())
            ->select('id', 'slug', 'updated_at')
            ->get()
            ->each(function (Person $person) use ($sitemap): void {
                $sitemap->add(
                    Url::create("/people/{$person->slug}")
                        ->setLastModificationDate($person->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                        ->setPriority(0.7)
                );
            });

        // Add investors
        $this->info('Adding investors to sitemap...');
        Investor::query()
            ->whereHas('companies', fn (Builder $query) => $query->approved())
            ->select('id', 'slug', 'updated_at')
            ->get()
            ->each(function (Investor $investor) use ($sitemap): void {
                $sitemap->add(
                    Url::create("/investors/{$investor->slug}")
                        ->setLastModificationDate($investor->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                        ->setPriority(0.7)
                );
            });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully!');
    }
}
