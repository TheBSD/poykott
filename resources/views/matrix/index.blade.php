<x-app-layout>
    <main class="container mx-auto min-h-screen space-y-6 p-6">
        <section>
            <h1 class="text-3xl font-bold">Matrix Alternatives</h1>

            <div class="w-full max-w-xl mx-auto text-center py-6">
                <form method="get" action="{{ route('matrix.index') }}" class="flex items-center gap-2">
                    <input id="company-search" name="company" list="companies-list" type="search" placeholder="Search company (e.g. Shopify)" class="w-full px-4 py-3 border border-gray-200 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-accent focus:border-accent" autocomplete="off" />
                    <button type="submit" class="px-4 py-3 bg-accent text-white rounded-md">Search</button>
                    <datalist id="companies-list">
                        @foreach ($companies as $c)
                            <option value="{{ $c['name'] }}"></option>
                        @endforeach
                    </datalist>
                </form>
                <p class="text-sm text-gray-600 mt-3">Search for a company to discover and compare alternative solutions with detailed feature matrices and regional presence data.</p>
            </div>
        </section>
        <section class="mt-6">
            @if (count($companies))
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($companies as $c)
                        <a href="{{ route('matrix.show', ['company' => $c['name']]) }}" class="flex flex-col items-start gap-3 p-4 border border-gray-100 rounded-lg hover:shadow-md transition-shadow bg-white">
                            <div class="flex items-center gap-3 w-full">
                                <div class="h-12 w-12 rounded-md bg-gray-50 flex items-center justify-center overflow-hidden">
                                    <img src="{{ $c['image_path'] }}" alt="{{ $c['name'] }} logo" class="h-10 w-10 object-contain" />
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold text-lg">{{ $c['name'] }}</div>
                                    <div class="text-sm text-gray-500">View comparison matrix</div>
                                </div>
                            </div>
                            {{-- <div class="mt-2 text-xs text-gray-400">CSV: <span class="text-gray-600">{{ $slug }}.csv</span></div> --}}
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No data for matrix comparisons found.</p>
            @endif
        </section>
    </main>
</x-app-layout>
