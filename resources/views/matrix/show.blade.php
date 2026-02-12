<x-app-layout>
    <main class="container mx-auto min-h-screen space-y-6 p-6">
        <section>
            <h1 class="text-2xl font-bold">{{ ucfirst($company) }} Alternatives</h1>
            <p class="text-sm text-gray-600">Compare {{ ucfirst($company) }} with alternative solutions. Click "Details" to view comprehensive information for each option.</p>
        </section>

        <section class="mt-6">
            @if (count($rows))
                @php
                    $bestScore = null;
                    foreach ($rows as $r) {
                        $isSearchedRow = isset($r['name']) && \Illuminate\Support\Str::slug($r['name']) === \Illuminate\Support\Str::slug($company);
                        if ($isSearchedRow) {
                            continue;
                        }

                        $score = isset($r['totalScore']) ? (int) $r['totalScore'] : 0;
                        if ($bestScore === null || $score > $bestScore) {
                            $bestScore = $score;
                        }
                    }
                @endphp

                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-3 py-2 text-left">Alternative</th>
                                <th class="px-3 py-2 text-left">Total</th>
                                <th class="px-3 py-2 text-left">Features</th>
                                <th class="px-3 py-2 text-left">Security</th>
                                <th class="px-3 py-2 text-left">Pricing</th>
                                <th class="px-3 py-2 text-left">ISL Presence</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $r)
                                @php
                                    $score = isset($r['totalScore']) ? (int) $r['totalScore'] : 0;
                                    $isBest = $score === $bestScore;
                                    $isSearched = isset($r['name']) && \Illuminate\Support\Str::slug($r['name']) === \Illuminate\Support\Str::slug($company);
                                    $logoPath = isset($r['logo']) ? asset('images/logos/' . $r['logo']) : '';
                                @endphp

                                <tr class="odd:bg-white even:bg-gray-50 {{ $isBest ? 'bg-accent/10 border-l-4 border-l-accent' : '' }}">
                                    <td class="px-3 py-3 align-middle">
                                        <div class="flex items-center gap-3">
                                            @if ($logoPath)
                                                <img src="{{ $logoPath }}" alt="{{ $r['name'] ?? '' }} logo" class="h-10 w-10 object-cover rounded-md" />
                                            @endif
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <div class="font-medium">{{ $r['name'] ?? '' }}</div>
                                                    @if ($isSearched)
                                                        <span class="text-xs bg-gray-100 border px-2 py-0.5 rounded text-gray-600">Searched</span>
                                                    @endif
                                                    @if ($isBest && !$isSearched)
                                                        <span class="text-xs bg-green-400 border border-green-400 px-2 py-0.5 rounded text-white-700">TOP</span>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $r['description'] ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-3 py-3 text-center align-middle">
                                        <div class="font-medium">{{ $score }}</div>
                                        <div class="text-xs text-gray-500">/100</div>
                                    </td>

                                    @php
                                        $colMaxes = [
                                            'features' => 25,
                                            'security' => 5,
                                            'pricing' => 30,
                                            'islPresence' => 40,
                                        ];
                                    @endphp
                                    @foreach (['features','security','pricing','islPresence'] as $col)
                                        @php
                                            $val = isset($r[$col]) ? (int)$r[$col] : null;
                                            $max = $colMaxes[$col] ?? 100;
                                        @endphp
                                        <td class="px-3 py-3 align-middle">
                                            @if ($val !== null)
                                                <div class="w-40 bg-gray-100 h-2 rounded overflow-hidden">
                                                    <div class="h-2 bg-green-500" style="width: {{ ($val/$max)*100 }}%;"></div>
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">{{ $val }}/{{ $max }}</div>
                                            @else
                                                <div class="text-sm text-gray-500">—</div>
                                            @endif
                                        </td>
                                    @endforeach

                                    <td class="px-3 py-3 align-middle">
                                        @if (isset($r['name']))
                                            <a href="{{ route('matrix.details', ['alternative' => \Illuminate\Support\Str::slug($r['name']), 'company' => $company]) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-200 rounded text-sm text-gray-700 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-accent">Details</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">No data found.</p>
            @endif
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
                            {{-- <p class="text-muted-foreground">Total Score: <span class="text-accent font-semibold">{{ $toPercent($selected['totalScore']) ?? $selected['totalScore'] }}%</span></p> --}}
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
                                        $orderedSections = [
                                            ['title' => 'Recommendation and Risk Summary', 'items' => ['Overall Risk Level', 'Best Use Case', 'Recommendation']],
                                            ['title' => 'Features', 'items' => ['Setup Complexity', 'Drag and Drop editing', 'AI Services', 'Specialized Plugins', 'All-in-one hosting', 'Access to code', 'E-Commerce tools']],
                                            ['title' => 'Security and Compliance', 'items' => ['Security and Compliance']],
                                            ['title' => 'Pricing', 'items' => ['Free tier', 'Team tier', 'Business tier']],
                                            ['title' => 'ISL Presence & Ties Assessment', 'items' => ['Headquarters', 'Major ISL Investment', 'ISL Partnerships', 'Data Centers', 'Founder/Leadership', 'Leadership Pro ISL Statements']],
                                        ];

                                        $renderCell = function($row, $key) {
                                            if (! $row) return '—';
                                            return $row[$key] ?? ($row[strtolower(str_replace(' ', '', $key))] ?? '—');
                                        };
                                    @endphp

                                    @foreach ($orderedSections as $section)
                                        <tr class="bg-gray-50">
                                            <td class="p-2 font-semibold">{{ $section['title'] }}</td>
                                            <td class="p-2"></td>
                                            <td class="p-2"></td>
                                        </tr>

                                        @foreach ($section['items'] as $item)
                                            <tr class="border-b">
                                                <td class="p-2 pl-6">{{ $item }}</td>
                                                <td class="p-2 {{ $selected ? 'bg-accent/10' : '' }}">{!! nl2br(e($renderCell($selected, $item))) !!}</td>
                                                <td class="p-2">{!! nl2br(e($renderCell($searched, $item))) !!}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </section>
        @endif
    </main>
</x-app-layout>
