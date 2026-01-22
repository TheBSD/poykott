<x-app-layout>
    <main class="container mx-auto min-h-screen space-y-6 p-6">
        <section>
            <h1 class="text-3xl font-bold">Matrix Alternatives</h1>
            <p class="text-sm text-gray-600">Select a company CSV to view its comparison matrix.</p>
        </section>

        <section class="mt-6">
            @if (count($companies))
                <ul class="space-y-2">
                    @foreach ($companies as $c)
                        @php
                            $logo = asset('images/logos/' . \Illuminate\Support\Str::slug($c['name']) . '.svg');
                        @endphp
                        <li>
                            <a href="{{ route('matrix.show', ['company' => $c['name']]) }}" class="flex items-center gap-3 text-blue-600 hover:underline">
                                <img src="{{ $logo }}" alt="{{ $c['name'] }} logo" class="h-8 w-8 object-cover rounded-md" />
                                <span class="font-medium">{{ $c['name'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">No matrix CSVs found. Place CSV files in <strong>storage/app/matrix</strong>.</p>
            @endif
        </section>
    </main>
</x-app-layout>
