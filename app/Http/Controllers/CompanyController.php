<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewCompanyRequest;
use App\Models\Company;
use App\Models\User;
use App\Notification\ReviewAlternative;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $data = $request->validated();

        $sanitized = collect($data)->map(function ($value) {
            return e(strip_tags(trim($value)));
        })->all();

        try {
            return DB::transaction(function () use ($sanitized, $newCompany) {
                $slug = Str::slug($sanitized['name']);

                if ($newCompany->where('slug', '=', $slug)->exists()) {
                    $slug .= '-' . Str::random(6);
                }

                $newCompany->create([
                    'name' => $sanitized['name'],
                    'slug' => $slug,
                    'email' => $sanitized['email'],
                    'personal_email' => $sanitized['p_email'],
                    'url' => $sanitized['url'],
                    'icon_url' => $sanitized['icon_url'],
                    'short_description' => $sanitized['short_description'],
                    'description' => $sanitized['description'],
                    'tags' => $sanitized['tags'],
                    'office_locations' => $sanitized['office_locations'],
                    'resources' => $sanitized['resources'],
                ]);

                return redirect()
                    ->back()
                    ->with('success', 'company successfully created');
            });
        } catch (Exception $e) {
            Log::error('Failed to create company:' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('camp_error', 'Failed to create company. please try again later');
        }
    }
}
