<?php

namespace App\Http\Controllers;

use App\Actions\FormatResourcesWikipediaStyleAction;
use App\Actions\SeoSetCompanyPageAction;
use App\Actions\SeoSetPageAction;
use App\Http\Requests\NewCompanyRequest;
use App\Models\Company;
use App\Models\User;
use App\Notification\ReviewAlternative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(SeoSetPageAction $seoSetPageAction)
    {
        $seoSetPageAction->execute(
            title: 'Companies',
            description: 'Some companies to boycott',
        );

        $companies = Company::query()
            ->with([
                'media',
                'tagsRelation' => function ($query): void {
                    $query->select('tags.id', 'name')->limit(3);
                },
            ])
            ->approved()
            ->simplePaginate(20, ['companies.id', 'name', 'description', 'short_description', 'slug', 'image_path']);

        return view('companies.index', ['companies' => $companies]);
    }

    public function create(SeoSetPageAction $seoSetPageAction)
    {
        $seoSetPageAction->execute(
            title: 'Add Company',
            description: 'Add a company to boycott',
        );

        return view('companies.create');
    }

    public function store(NewCompanyRequest $request, Company $company)
    {
        $data = $request->validated();

        $company->create([
            'name' => $data['name'],
            'url' => $data['url'],
            'description' => $data['description'],
        ]);

        return to_route('companies.index')
            ->with('success', 'company successfully created. Wait for approval');
    }

    public function show(
        Request $request,
        Company $company,
        FormatResourcesWikipediaStyleAction $formatResourcesAction,
        SeoSetCompanyPageAction $seoSetCompanyPageAction
    ): View {
        abort_if(! $company->approved_at, 404);

        $seoSetCompanyPageAction->execute($company);

        $company->load([
            'founders:id,name,slug',
            'resources:id,resourceable_id,url',
            'officeLocations:id,name',
            'tagsRelation:id,name',
            'aiAlternative',
            'investors' => function ($query): void {
                $query->approved()->select('id', 'name', 'slug');
            },
            'alternatives' => function ($query): void {
                $query->approved()->select('id', 'name', 'description', 'url');
            },
        ]);

        $resources = $formatResourcesAction->execute($company->resources);

        return view('companies.show', ['company' => $company, 'resources' => $resources]);
    }

    public function redirectToSlug(Request $request, $companyUrl)
    {
        $parsedUrl = parse_url((string) $companyUrl, PHP_URL_HOST) ?: $companyUrl;
        $parsedUrl = preg_replace('/^www\./', '', (string) $parsedUrl);

        $company = Company::query()->where('url', 'LIKE', '%' . $parsedUrl . '%')->first();

        // If company not found or not approved, show the fallback page
        if (! $company || ! $company->approved_at) {
            $fullCompanyUrl = Str::start($parsedUrl, 'https://');

            return view('companies.not-found-url', [
                'parsedUrl' => $parsedUrl,
                'url' => $fullCompanyUrl,
                'name' => $request->name,
            ]);
        }

        return redirect()->route('companies.show', ['company' => $company->slug], 301);
    }

    public function storeAlternative(Request $request, Company $company)
    {
        // validation
        $validated = $request->validate([
            'name' => ['required', 'min:2', 'max:255'],
            'url' => ['required', 'url', 'active_url', 'max:255'],
        ]);

        $alternative = $company->alternatives()->create([
            'name' => $validated['name'],
            'url' => $validated['url'],
        ]);

        $admin = User::query()->first();
        Notification::send($admin, new ReviewAlternative($alternative, $company));

        return redirect()->back()->with('success', 'Thank you for suggesting an alternative');
    }

    public function redirect(Company $company): RedirectResponse
    {
        return redirect()->route('companies.show', $company, 301);
    }
}
