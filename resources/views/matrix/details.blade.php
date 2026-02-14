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
                                            ['title' => 'Security and Compliance', 'items' => []],
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
        @else
            <p class="text-gray-500">No alternative selected or not found.</p>
        @endif
    </main>
</x-app-layout>
