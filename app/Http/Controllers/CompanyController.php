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
            'alternatives:id,name,description,url',
            'founders:id,name,avatar',
            'resources:id,resourceable_id,url',
            'officeLocations:id,name',
            'logo:id,imageable_id,path',
            'tagsRelation:id,name',
            'investors' => function ($query) {
                $query->with('logo:id,imageable_id,path')->select('id', 'name');
            },
        ]);

        return view('companies.show', compact('company'));
    }
}
