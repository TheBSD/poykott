<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyStoreRequest;
use App\Jobs\AddCompany;
use App\Models\Company;
use App\Models\User;
use App\Notification\ReviewCompany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{

    public function show(Request $request, Company $company): View
    {
        $company->load([
            'founders:id,name,avatar',
            'resources:id,resourceable_id,url',
            'officeLocations:id,name',
            'logo:id,imageable_id,path',
            'tagsRelation:id,name',
            'investors' => function ($query) {
                $query->with('logo:id,imageable_id,path')->select('id', 'name');
            },
            'alternatives' => function ($query) {
                $query->approved()->select('id', 'name', 'description', 'url');
            },
        ]);

        return view('companies.show', compact('company'));
    }

    public function storeAlternative(Request $request, Company $company)
    {
        $company->alternatives()->create([
            'name' => $request->name,
            'url' => $request->url,
        ]);
        return redirect()->back()->with('success', 'Thank you for suggesting an alternative');
    }
}
