<?php

namespace App\Http\Controllers;

use App\Actions\GenerateCompanyAiAlternativesAction;
use App\Models\Company;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class AiAlternativeController extends Controller
{
    public function store(Company $company, GenerateCompanyAiAlternativesAction $action): JsonResponse
    {
        try {
            $aiAlternative = $action->execute($company);

            return response()->json([
                'success' => true,
                'content' => $aiAlternative->content,
                'html' => Str::markdown($aiAlternative->content),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate alternatives: ' . $e->getMessage(),
            ], 500);
        }
    }
}
