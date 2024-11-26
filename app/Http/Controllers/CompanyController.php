<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function show(Request $request, Company $company): View
    {
        $company->load([
            'founders:id,name,avatar,slug',
            'resources:id,resourceable_id,url',
            'officeLocations:id,name',
            'tagsRelation:id,name',
            'investors' => function ($query): void {
                $query->with('logo:id,imageable_id,path')->select('id', 'name');
            },
            'media' => function ($query) {
                $query->select('id', 'model_id', 'model_type', 'disk', 'file_name', 'generated_conversions','collection_name');
            },
            'alternatives' => function ($query): void {
                $query->approved()->select('id', 'name', 'description', 'url');
            },
        ]);

        return view('companies.show', ['company' => $company]);
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
