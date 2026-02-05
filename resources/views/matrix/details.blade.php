<x-app-layout>
    <main class="container mx-auto min-h-screen space-y-6 p-6">
        <section>
            <h1 class="text-2xl font-bold">Alternative Details</h1>
            <p class="text-sm text-gray-600">Detailed comparison between the selected alternative and the searched company.</p>
        </section>

        @if ($selected)
            @php
                $selectedLogo = isset($selected['logo']) ? asset('images/logos/' . $selected['logo']) : null;
                $searched = $searchedCompany ?? null;
                $searchedLogo = $searched && isset($searched['logo']) ? asset('images/logos/' . $searched['logo']) : null;
                $toPercent = function($score) {
                    if ($score === null) return null;
                    $s = (int)$score;
                    return round(($s / 25) * 100);
                };
            @endphp

            <section class="space-y-8">
                <div class="flex justify-between gap-4 pb-4 border-b border-border">
                    <div class="flex items-center gap-4 border-l-4 border-l-accent pl-4">
                        <div class="relative h-16 w-16 rounded-lg overflow-hidden bg-secondary flex-shrink-0">
                            @if ($selectedLogo)
                                <img src="{{ $selectedLogo }}" alt="{{ $selected['name'] }} logo" class="object-contain p-2 h-full w-full" />
                            @endif
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold">{{ $selected['name'] ?? 'Selected' }}</h2>
                            @php $selectedPercent = $toPercent($selected['totalScore']) ?? null; @endphp
                            <p class="text-muted-foreground">Total Score: <span class="text-accent font-semibold">{{ $selectedPercent !== null ? $selectedPercent . '%' : '—' }}</span></p>
                            @if (isset($selected['website']))
                                <a target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-sm text-accent hover:underline mt-1" href="{{ $selected['website'] }}">Visit Website <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg></a>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-right">{{ $searched['name'] ?? $company }}</h2>
                            <p class="text-muted-foreground text-right">Total Score: <span class="text-foreground font-semibold">{{ $toPercent($searched['totalScore'] ?? null) ?? ($searched['totalScore'] ?? '—') }}%</span></p>
                        </div>
                        <div class="relative h-16 w-16 rounded-lg overflow-hidden bg-secondary flex-shrink-0">
                            @if ($searchedLogo)
                                <img src="{{ $searchedLogo }}" alt="{{ $searched['name'] ?? '' }} logo" class="object-contain p-2 h-full w-full" />
                            @endif
                        </div>
                    </div>
                </div>

                <section>
                    <h3 class="text-xl font-semibold mb-4">Evidence / Presence in Israel</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-card border rounded-lg p-4 border-accent">
                            <p class="text-sm font-medium text-accent mb-2">{{ $selected['name'] }}</p>
                            <p class="text-muted-foreground">{{ $selected['presence'] ?? $selected['description'] ?? 'No presence data available.' }}</p>
                        </div>
                        <div class="bg-card border border-border rounded-lg p-4">
                            <p class="text-sm font-medium text-muted-foreground mb-2">{{ $searched['name'] ?? $company }}</p>
                            <p class="text-muted-foreground">{{ $searched['presence'] ?? $searched['description'] ?? 'No presence data available.' }}</p>
                        </div>
                    </div>
                </section>

                <section>
                    <h3 class="text-xl font-semibold mb-4">Functionalities</h3>
                    <div class="overflow-x-auto">
                        <div class="relative w-full overflow-x-auto">
                            <table class="w-full caption-bottom text-sm">
                                <thead class="[&_tr]:border-b">
                                    <tr class="border-b">
                                        <th class="text-foreground h-10 px-2 text-left font-medium w-1/3">Feature</th>
                                        <th class="h-10 px-2 text-left font-medium w-1/3 text-accent">{{ $selected['name'] }}</th>
                                        <th class="text-foreground h-10 px-2 text-left font-medium w-1/3">{{ $searched['name'] ?? $company }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $features = [
                                            'serverlessFunctions' => 'Serverless Functions',
                                            'previewEnvironments' => 'Preview Environments',
                                            'edgeCaching' => 'Edge Caching',
                                            'backendIntegration' => 'Backend Integration',
                                            'performanceMonitoring' => 'Performance Monitoring',
                                        ];
                                        $renderFeature = function($row, $key) {
                                            if (!$row) return '—';
                                            if (isset($row[$key]) && $row[$key] !== '') return $row[$key];
                                            return '—';
                                        };
                                    @endphp
                                    @foreach ($features as $key => $label)
                                        <tr class="border-b">
                                            <td class="p-2 font-medium">{{ $label }}</td>
                                            <td class="p-2 {{ $selected ? 'bg-accent/10' : '' }}">{{ $renderFeature($selected, $key) }}</td>
                                            <td class="p-2">{{ $renderFeature($searched, $key) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <section>
                    <h3 class="text-xl font-semibold mb-4">Summary</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-card border rounded-lg p-4">
                            <p class="text-sm font-medium mb-2">Implementation & Setup</p>
                            <p class="text-muted-foreground">Setup: {{ $selected['setupComplexity'] ?? ($selected['easeOfSetup'] ?? '—') }}</p>
                            <p class="text-muted-foreground">Developer Experience: {{ $selected['developerExperience'] ?? '—' }}</p>
                        </div>
                        <div class="bg-card border rounded-lg p-4">
                            <p class="text-sm font-medium mb-2">Pricing</p>
                            <p class="text-muted-foreground">{{ $selected['pricingNotes'] ?? (($selected['pricing'] ?? null) ? $selected['pricing'] : 'Pricing details not available') }}</p>
                        </div>
                    </div>
                </section>

                <section>
                    <h3 class="text-xl font-semibold mb-4">Details</h3>
                    <div class="overflow-x-auto">
                        <div class="relative w-full overflow-x-auto">
                            <table class="w-full caption-bottom text-sm">
                                <thead class="[&_tr]:border-b">
                                    <tr class="border-b">
                                        <th class="text-foreground h-10 px-2 text-left font-medium w-1/3">Metric</th>
                                        <th class="h-10 px-2 text-left font-medium w-1/3 text-accent">{{ $selected['name'] }}</th>
                                        <th class="text-foreground h-10 px-2 text-left font-medium w-1/3">{{ $searched['name'] ?? $company }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $metrics = [
                                            'founded' => 'Founded',
                                            'employees' => 'Employees',
                                            'primaryUseCase' => 'Primary Use Case',
                                            'targetUsers' => 'Target Users',
                                            'fundingToDate' => 'Funding to Date',
                                            'revenue2024' => '2024 Revenue',
                                            'growthRate' => 'Growth Rate',
                                        ];
                                    @endphp
                                    @foreach ($metrics as $key => $label)
                                        <tr class="border-b">
                                            <td class="p-2 font-medium">{{ $label }}</td>
                                            <td class="p-2 {{ $selected ? 'bg-accent/10' : '' }}">{{ $selected[$key] ?? '—' }}</td>
                                            <td class="p-2">{{ $searched[$key] ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </section>
        @else
            <p class="text-gray-500">No alternative selected or not found.</p>
        @endif
    </main>
</x-app-layout>
