<?php

namespace App\Http\Controllers;

use App\Actions\FormatResourcesWikipediaStyleAction;
use App\Actions\SeoSetAlternativePageAction;
use App\Actions\SeoSetPageAction;
use App\Models\Alternative;
use App\Models\Company;
use App\Models\ContactMessage;
use App\Models\Investor;
use App\Models\SimilarSiteCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(SeoSetPageAction $seoSetPageAction)
    {
        $seoSetPageAction->execute(
            title: 'Alternatives',
            description: 'Some alternatives to Israeli companies',
        );

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

    public function show(
        Request $request,
        Alternative $alternative,
        FormatResourcesWikipediaStyleAction $formatResourcesAction,
        SeoSetAlternativePageAction $seoSetAlternativePageAction
    ): View {
        abort_if(! $alternative->approved_at, 404);

        $seoSetAlternativePageAction->execute($alternative);

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

    public function about(SeoSetPageAction $seoSetPageAction)
    {
        $seoSetPageAction->execute(
            title: 'About Us',
            description: 'A website to know Israeli tech that powers a genocide. Get their alternatives and know more about similar sites that support the ethical use of technology.',
        );

        $stats = [];

        $stats['companies'] = Company::approved()->count();
        $stats['investors'] = Investor::approved()->count();
        $stats['alternatives'] = Alternative::approved()->count();

        return view('pages.about', ['stats' => $stats]);
    }

    public function contactPost(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string|min:10|max:10000',
        ]);

        ContactMessage::query()->create($validated);

        return back()->with('success', 'Message sent successfully');
    }

    public function contactGet(SeoSetPageAction $seoSetPageAction)
    {
        $seoSetPageAction->execute(
            title: 'Contact us',
            description: 'Contact us for any suggestions or bugs, we will be happy to help you',
        );

        return view('pages.contact');
    }

    public function similarSites(SeoSetPageAction $seoSetPageAction)
    {
        $seoSetPageAction->execute(
            title: 'Similar Sites',
            description: 'Here we focus on boycotting Israeli tech. Many allied organizations work on boycotting goods that support Israel , educating about Palestinian rights , and supporting Palestine . While we share common goals, we are not responsible for their content.',
        );

        $similarSitesCategories = SimilarSiteCategory::query()->with('similarSites')->get();

        return view('pages.similar-sites', ['similarSitesCategories' => $similarSitesCategories]);
    }

    public function newsletter(SeoSetPageAction $seoSetPageAction)
    {
        $seoSetPageAction->execute(
            title: 'Newsletter',
            description: 'Sign up for our newsletter to get updates on our latest news and announcements.',
        );

        return view('pages.newsletter');
    }
}
