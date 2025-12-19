<?php

namespace App\Actions;

use App\Models\AiAlternative;
use App\Models\Company;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;
use Prism\Prism\ValueObjects\Messages\SystemMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;

class GenerateCompanyAiAlternativesAction
{
    protected function buildPrompt(Company $company): string
    {
        $tags = $company->tagsRelation->pluck('name')->implode(', ');

        return view('prompts.company-alternatives', [
            'company' => $company,
            'description' => $company->description ?: null,
            'tags' => $tags ?: null,
        ])->render();
    }

    public function execute(Company $company): AiAlternative
    {
        $prompt = $this->buildPrompt($company);

        $response = Prism::text()
            ->using(Provider::Mistral, config('prism.providers.mistral.model'))
            ->withMessages([
                new SystemMessage('You are an expert analyst specializing in finding alternative products and services.'),
                new UserMessage($prompt),
            ])
            ->withMaxTokens(config('prism.providers.mistral.max_tokens'))
            ->usingTemperature(config('prism.providers.mistral.temperature'))
            ->asText();

        return AiAlternative::query()->updateOrCreate(['company_id' => $company->id], [
            'content' => $response->text,
            'model_used' => config('prism.providers.mistral.model'),
            'prompt_tokens' => $response->usage->promptTokens,
            'completion_tokens' => $response->usage->completionTokens,
        ]);
    }
}
