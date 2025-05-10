<?php

namespace App\Http\Controllers;

use App\Actions\FormatResourcesAction;
use App\Models\Alternative;
use App\Models\Company;
use App\Models\ContactMessage;
use App\Models\Investor;
use App\Models\SimilarSiteCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index()
    {
        $alternatives = Alternative::query()
            ->with([
                'media',
                'tagsRelation' => function ($query): void {
                    $query->select('tags.id', 'name')->limit(3);
                },
            ])->approved()
            ->simplePaginate(20, ['alternatives.id', 'name', 'description', 'short_description', 'slug', 'image_path']);

        return view('home', ['alternatives' => $alternatives]);
    }

    public function show(Request $request, Alternative $alternative, FormatResourcesAction $formatResourcesAction): View
    {
        abort_if(! $alternative->approved_at, 404);

        $alternative->load([
            'resources:id,resourceable_id,url',
            'tagsRelation:id,name',
            'companies' => function ($query): void {
                $query->approved()->select('id', 'name', 'description', 'url');
            },
        ]);

        $resources = $formatResourcesAction->execute($alternative->resources);

        return view('alternatives.show', ['alternative' => $alternative, 'resources' => $resources]);
    }

    public function about()
    {
        $stats = [];

        $stats['companies'] = Company::approved()->count();
        $stats['investors'] = Investor::approved()->count();
        $stats['alternatives'] = Alternative::approved()->count();

        return view('pages.about', ['stats' => $stats]);
    }

    public function contact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string|min:10|max:10000',
        ]);

        ContactMessage::query()->create($validated);

        return back()->with('success', 'Message sent successfully');
    }

    public function similarSites()
    {
        $similarSitesCategories = SimilarSiteCategory::query()->with('similarSites')->get();

        return view('pages.similar-sites', ['similarSitesCategories' => $similarSitesCategories]);
    }
}
