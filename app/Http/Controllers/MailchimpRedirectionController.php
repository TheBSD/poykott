<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class MailchimpRedirectionController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        return redirect()->route('alternatives.index')->with('success', 'Thank you for your subscription!');
    }
}
