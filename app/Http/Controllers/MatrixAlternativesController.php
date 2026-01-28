<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Support\Str;

class MatrixAlternativesController extends Controller
{
    // List available company CSVs in storage/app/matrix
    public function index()
    {
        // If user submitted a company via query string, redirect to the show route.
        $company = request()->query('company');
        if (! empty($company)) {
            return redirect()->route('matrix.show', ['company' => $company]);
        }

        $dir = storage_path('app/matrix');
        $files = [];

        if (is_dir($dir)) {
            foreach (glob($dir . '/*.csv') as $path) {
                $name = pathinfo($path, PATHINFO_FILENAME);
                $slug = Str::slug($name);

                // Try to find a Company model so we can use the media library image_path.
                $companyModel = Company::query()->where('slug', $slug)
                    ->orWhere('name', $name)
                    ->first();

                $imagePath = $companyModel?->image_path ?? asset('images/logos/' . $slug . '.svg');

                $files[] = [
                    'slug' => $slug,
                    'name' => $name,
                    'image_path' => $imagePath,
                ];
            }
        }

        return view('matrix.index', ['companies' => $files]);
    }

    // Show matrix for a company and optional alternative detail
    public function show(string $company, $alternative = null)
    {
        $slug = Str::slug($company);
        $dir = storage_path('app/matrix');
        $file = $dir . '/' . $company . '.csv';

        // fallback: try slug -> original filename discovery
        if (! file_exists($file)) {
            foreach (glob($dir . '/*.csv') as $path) {
                $name = pathinfo($path, PATHINFO_FILENAME);
                if (Str::slug($name) === $slug) {
                    $file = $path;
                    break;
                }
            }
        }

        abort_unless(file_exists($file), 404, 'Matrix CSV not found for ' . $company);

        $rows = [];
        if (($handle = fopen($file, 'r')) !== false) {
            $headers = fgetcsv($handle);
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) === count($headers)) {
                    $rows[] = array_combine($headers, $row);
                }
            }
            fclose($handle);
        }

        $selected = null;
        foreach ($rows as $r) {
            if (isset($r['name']) && Str::slug($r['name']) === Str::slug($alternative)) {
                $selected = $r;
                break;
            }
        }

        // find the searched company row (the company being compared)
        $searchedCompany = null;
        foreach ($rows as $r) {
            if (isset($r['name']) && Str::slug($r['name']) === Str::slug($company)) {
                $searchedCompany = $r;
                break;
            }
        }

        return view('matrix.show', [
            'company' => $company,
            'rows' => $rows,
            'selected' => $selected,
            'searchedCompany' => $searchedCompany,
        ]);
    }
}
