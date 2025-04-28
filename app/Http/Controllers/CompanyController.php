<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewCompanyRequest;
use App\Models\Company;
use App\Models\User;
use App\Notification\ReviewAlternative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function create()
    {
        return view('companies.create');
    }

    public function store(NewCompanyRequest $request, Company $company)
    {
        $data = $request->validated();

        $company->create([
            'name' => $data['name'],
            'url' => $data['url'],
            'description' => $data['description'],
            'notes' => $data['notes'],
        ]);

        return to_route('home')
            ->with('success', 'company successfully created. Wait for approval');
    }

    public function show(Request $request, Company $company): View
    {
        abort_if(! $company->approved_at, 404);

        $company->load([
            'founders:id,name,slug',
            'resources:id,resourceable_id,url',
            'officeLocations:id,name',
            // 'logo:id,imageable_id,path',
            'tagsRelation:id,name',
            'investors' => function ($query): void {
                $query->approved()->select('id', 'name');
            },
            'alternatives' => function ($query): void {
                $query->approved()->select('id', 'name', 'description', 'url');
            },
        ]);

        return view('companies.show', ['company' => $company]);
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
        $alternative = $company->alternatives()->create([
            'name' => $request->name,
            'url' => $request->url,
        ]);

        $admin = User::query()->first();
        Notification::send($admin, new ReviewAlternative($alternative, $company));

        return redirect()->back()->with('success', 'Thank you for suggesting an alternative');
    }
}
