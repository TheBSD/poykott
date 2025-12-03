<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class OpCacheController extends Controller
{
    /**
     * Display opcache-gui interface
     */
    public function __invoke(): Response
    {
        abort_if(! auth()->check() || ! auth()->user()->hasRole('super_admin'), 403, 'Unauthorized');

        // Path to the opcache-gui index.php
        $indexPath = base_path('vendor/amnuts/opcache-gui/index.php');

        abort_unless(file_exists($indexPath), 404, 'OpCache GUI not found');

        // Start output buffering to capture the output
        ob_start();

        // Include and execute the opcache-gui index.php
        include $indexPath;

        $content = ob_get_clean();

        return response($content, 200)
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }
}
