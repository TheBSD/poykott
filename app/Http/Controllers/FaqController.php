<?php

namespace App\Http\Controllers;

use App\Actions\SeoSetFaqsPageAction;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function __invoke(Request $request, SeoSetFaqsPageAction $seoSetFaqsPageAction): View
    {
        $faqs = Faq::all();

        $seoSetFaqsPageAction->execute($faqs);

        return view('pages.faqs', ['faqs' => $faqs]);
    }
}
