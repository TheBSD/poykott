<?php

namespace App\Http\Controllers;

use App\Actions\FormatResourcesAction;
use App\Actions\SeoSetInvestorPageAction;
use App\Models\Investor;

class InvestorController extends Controller
{
    public function index()
    {
        $investors = Investor::query()
            ->with([
                'media',
                'tagsRelation' => function ($query): void {
                    $query->select('tags.id', 'tags.name');
                },
            ])
            ->paginate(20, ['investors.id', 'name', 'description', 'slug']);

        return view('investors.index', ['investors' => $investors]);
    }

    public function show(
        Investor $investor,
        FormatResourcesAction $formatResourcesAction,
        SeoSetInvestorPageAction $seoSetInvestorPageAction
    ) {
        $seoSetInvestorPageAction->execute($investor);

        $investor->load([
            'resources:id,resourceable_id,url',
            'companies' => function ($query): void {
                $query->whereNotNull('approved_at')
                    ->select(['id', 'name', 'description', 'slug', 'url', 'approved_at']);
            },
            'companies.media',
        ]);

        $resources = $formatResourcesAction->execute($investor->resources);

        return view('investors.show', ['investor' => $investor, 'resources' => $resources]);
    }
}
