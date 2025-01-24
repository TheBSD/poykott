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
    public function show(Request $request, Company $company): View
    {
        abort_if(! $company->approved_at, 404);

        $company->load([
            'founders:id,name,slug',
            'resources:id,resourceable_id,url',
            'officeLocations:id,name',
            //'logo:id,imageable_id,path',
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

    public function storeNewCompany(NewCompanyRequest $request, Company $newCompany)
    {
        $validated = collect($request->validated());

        $newCompany->create([
            'name' => e(strip_tags(trim($validated->pluck('name')))),
            'slug' => Str::slug($validated->pluck('name')),
            'email' => e(strip_tags(trim($validated->pluck('email')))),
            'personal_email' => e(strip_tags(trim($validated->pluck('p_email')))),
            'url' => e(strip_tags(trim($validated->pluck('url')))),
            'icon_url' => e(strip_tags(trim($validated->pluck('icon_url')))),
            'short_description' => e(strip_tags(trim($validated->pluck('short_description')))),
            'description' => e(strip_tags(trim($validated->pluck('description')))),
            'tags' => e(strip_tags(trim($validated->pluck('tags')))),
            'office_locations' => e(strip_tags(trim($validated->pluck('office_locations')))),
            'resources' => e(strip_tags(trim($validated->pluck('resources')))),
        ]);

        return redirect()->back();
    }
}
