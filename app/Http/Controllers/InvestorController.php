<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use Illuminate\Http\Request;

class InvestorController extends Controller
{
    public function index()
    {
        $investors = Investor::query()->paginate(20, ['investors.id', 'name', 'description']);

        return view('investors.index', ['investors' => $investors]);
    }

    public function loadMore(Request $request)
    {
        $investors = Investor::query()->paginate(20, ['investors.id', 'name', 'description'], 'page', $request->page);

        return response()->json(['investors' => $investors]);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $investors = Investor::query()->where('name', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")
            ->paginate(40, ['investors.id', 'name', 'description']);

        return response()->json(['investors' => $investors]);
    }
}
