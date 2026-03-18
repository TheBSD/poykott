<x-app-layout>
    <main class="container mx-auto min-h-screen space-y-6 p-6">
        <section>
            <div class="flex items-center gap-3 mb-1">
                <div class="h-10 w-10 rounded-md bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
                    <img src="{{ $logoPath }}" alt="{{ ucfirst($company) }} logo" class="h-8 w-8 object-contain" />
                </div>
                <h1 class="text-2xl font-bold">{{ ucfirst($company) }} Alternatives</h1>
            </div>
            <p class="text-sm text-gray-600">Compare {{ ucfirst($company) }} with alternative solutions. Click "Details" to view comprehensive information for each option.</p>
        </section>

        <section class="mt-6">
            @if (count($rows))
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
                                <tr class="odd:bg-white even:bg-gray-50 {{ $r['isBest'] ? 'bg-accent/10 border-l-4 border-l-accent' : '' }}">
                                    <td class="px-3 py-3 align-middle">
                                        <div class="flex items-center gap-3">
                                            @if ($r['logoPath'])
                                                <img src="{{ $r['logoPath'] }}" alt="{{ $r['name'] ?? '' }} logo" class="h-10 w-10 object-cover rounded-md" />
                                            @endif
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <div class="font-medium">{{ $r['name'] ?? '' }}</div>
                                                    @if ($r['isSearched'])
                                                        <span class="text-xs bg-gray-100 border px-2 py-0.5 rounded text-gray-600">Searched</span>
                                                    @endif
                                                    @if ($r['isBest'] && !$r['isSearched'])
                                                        <span class="text-xs bg-green-400 border border-green-400 px-2 py-0.5 rounded text-white-700">TOP</span>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $r['description'] ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-3 py-3 text-center align-middle">
                                        <div class="font-medium">{{ $r['score'] }}</div>
                                        <div class="text-xs text-gray-500">/100</div>
                                    </td>

                                    @foreach (['features','security','pricing','islPresence'] as $col)
                                        @php
                                            $val = isset($r[$col]) ? (int) $r[$col] : null;
                                            $max = $columnWeights[$col] ?? 100;
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

    </main>
</x-app-layout>
