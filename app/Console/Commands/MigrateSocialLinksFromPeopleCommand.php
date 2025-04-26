<?php

namespace App\Console\Commands;

use App\Models\Person;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class MigrateSocialLinksFromPeopleCommand extends Command
{
    protected $signature = 'migrate-social-links-from-people';

    protected $description = 'Migrate all social media links from people table to social_links table with relationship';

    /**
     * Execute the console command.
     *
     * @throws Throwable
     */
    public function handle(): void
    {
        DB::transaction(function (): void {
            Log::info('Starting transaction to move social links.');

            $people = Person::query()->get();

            Log::info('Fetched people.', ['count' => $people->count()]);

            $people->each(function (Person $person): void {
                Log::info('Processing person.', ['person_id' => $person->id]);

                foreach ($person->social_links ?? [] as $socialLink) {
                    Log::info('Processing social link.', [
                        'person_id' => $person->id,
                        'social_link' => $socialLink,
                    ]);

                    $person->socialLinks()->updateOrCreate([
                        'url' => $socialLink,
                    ], []);

                    Log::info('Social link saved/updated.', [
                        'person_id' => $person->id,
                        'social_link' => $socialLink,
                    ]);
                }

                $person->update(['social_links' => null]);

                Log::info('Cleared social_links field.', ['person_id' => $person->id]);
            });

            Log::info('Transaction complete.');
        });
    }
}
