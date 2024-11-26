<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use Illuminate\Http\Request;

class InvestorController extends Controller
{
    public function index()
    {
        $investors = Investor::query()
            ->with([
                'tagsRelation' => function ($query) {
                    $query->select('tags.id', 'tags.name');
                },
                'media' => function ($query) {
                    $query->select('id', 'model_id', 'model_type', 'disk', 'file_name', 'generated_conversions', 'collection_name');
                }])
            ->paginate(20, ['investors.id', 'name', 'description', 'slug']);

        return view('investors.index', ['investors' => $investors]);
    }

    public function show(Investor $investor)
    {
        $investor->load([
            'resources:id,resourceable_id,url',
            'companies' => function ($query): void {
                $query->with([
                    'media' => function ($query) {
                        $query->select('id', 'model_id', 'model_type', 'disk', 'file_name', 'generated_conversions', 'collection_name');
                    }])
                    ->select('id', 'name', 'description', 'slug');
            },
        ]);

        return view('investors.show', ['investor' => $investor]);
    }

    public function loadMore(Request $request)
    {
        $investors = Investor::query()
            ->with([
                'tagsRelation' => function ($query) {
                    $query->select('tags.id', 'tags.name');
                },
                'media' => function ($query) {
                    $query->select('id', 'model_id', 'model_type', 'disk', 'file_name', 'generated_conversions', 'collection_name');
                }])
            ->paginate(20, ['investors.id', 'name', 'description', 'slug'], 'page', $request->page);

        return response()->json(['investors' => $investors]);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $investors = Investor::query()
            ->with([
                'tagsRelation' => function ($query) {
                    $query->select('tags.id', 'tags.name');
                },
                'media' => function ($query) {
                    $query->select('id', 'model_id', 'model_type', 'disk', 'file_name', 'generated_conversions', 'collection_name');
                }])
            ->where('name', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")
            ->paginate(40, ['investors.id', 'name', 'description', 'slug']);

        return response()->json(['investors' => $investors]);
    }
}
