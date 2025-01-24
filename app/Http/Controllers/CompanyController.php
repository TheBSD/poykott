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
        $validated = $request->validated();
        extract($validated);

        $newCompany->create([
            'name' => e(strip_tags(trim((string) $name))),
            'slug' => Str::slug($name),
            'email' => e(strip_tags(trim((string) $email))),
            'personal_email' => e(strip_tags(trim((string) $p_email))),
            'url' => e(strip_tags(trim((string) $url))),
            'icon_url' => e(strip_tags(trim((string) $icon_url))),
            'short_description' => e(strip_tags(trim((string) $short_description))),
            'description' => e(strip_tags(trim((string) $description))),
            'tags' => e(strip_tags(trim((string) $tags))),
            'office_locations' => e(strip_tags(trim((string) $office_locations))),
            'resources' => e(strip_tags(trim((string) $resources))),
        ]);

        return redirect()->back();
    }
}
