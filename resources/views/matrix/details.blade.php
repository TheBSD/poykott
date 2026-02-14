<x-app-layout>
    <main class="container mx-auto min-h-screen space-y-6 p-6">
        <section>
            <h1 class="text-2xl font-bold">Alternative Details</h1>
            <p class="text-sm text-gray-600">Detailed comparison between the selected alternative and the searched company.</p>
        </section>

        @if ($selected)
            <section class="space-y-8">
                <div class="flex justify-between gap-4 pb-4 border-b border-border">
                    <div class="flex items-center gap-4 border-l-4 border-l-accent pl-4">
                        <div class="relative h-16 w-16 rounded-lg overflow-hidden bg-secondary flex-shrink-0">
                            @if ($comparisonData['selectedLogo'])
                                <img src="{{ $comparisonData['selectedLogo'] }}" alt="{{ $selected['name'] }} logo" class="object-contain p-2 h-full w-full" />
                            @endif
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold">{{ $selected['name'] ?? 'Selected' }}</h2>
                            <p class="text-muted-foreground">Total Score: <span class="text-accent font-semibold">{{ $comparisonData['selectedPercent'] !== null ? $comparisonData['selectedPercent'] . '%' : '—' }}</span></p>
                            @if (isset($selected['website']))
                                <a target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-sm text-accent hover:underline mt-1" href="{{ $selected['website'] }}">Visit Website <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg></a>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-right">{{ $searchedCompany['name'] ?? $company }}</h2>
                            <p class="text-muted-foreground text-right">Total Score: <span class="text-foreground font-semibold">{{ $comparisonData['searchedPercent'] !== null ? $comparisonData['searchedPercent'] . '%' : '—' }}</span></p>
                        </div>
                        <div class="relative h-16 w-16 rounded-lg overflow-hidden bg-secondary flex-shrink-0">
                            @if ($comparisonData['searchedLogo'])
                                <img src="{{ $comparisonData['searchedLogo'] }}" alt="{{ $searchedCompany['name'] ?? '' }} logo" class="object-contain p-2 h-full w-full" />
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
                                        <th class="text-foreground h-10 px-2 text-left font-medium">Metric</th>
                                        <th class="h-10 px-2 text-right font-medium text-accent w-16">{{ $selected['name'] }} Score</th>
                                        <th class="h-10 px-2 text-left font-medium text-accent flex-1">{{ $selected['name'] }} Details</th>
                                        <th class="h-10 px-2 text-right font-medium w-16">{{ $searchedCompany['name'] ?? $company }} Score</th>
                                        <th class="text-foreground h-10 px-2 text-left font-medium flex-1">{{ $searchedCompany['name'] ?? $company }} Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderedSections as $section)
                                        <tr class="bg-gray-50">
                                            <td class="p-2 font-semibold">{{ $section['title'] }}</td>
                                            <td class="p-2"></td>
                                            <td class="p-2"></td>
                                        </tr>

                                        @foreach ($section['items'] as $item)
                                            <tr class="border-b">
                                                <td class="p-2 pl-6">{{ $item }}</td>
                                                <td class="p-2 {{ $selected ? 'bg-accent/10' : '' }} text-right text-sm">{{ $getCellScore($selected, $item) }}</td>
                                                <td class="p-2 {{ $selected ? 'bg-accent/10' : '' }}">{!! nl2br(e($renderCellValue($selected, $item))) !!}</td>
                                                <td class="p-2 text-right text-sm">{{ $getCellScore($searchedCompany, $item) }}</td>
                                                <td class="p-2">{!! nl2br(e($renderCellValue($searchedCompany, $item))) !!}</td>
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
