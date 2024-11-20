<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AppLayout extends Component
{
    public function __construct()
    {
        //
    }

    public function render()
    {
        return view('components.app-layout');
    }
} 