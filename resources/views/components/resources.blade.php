@props([
    'resources',
    'class' => '',
])

@if (count($resources) > 0)
    <div class="{{ $class }} pt-8">
        <h2 class="mb-6 text-2xl font-bold text-gray-900">References</h2>

        <!-- Wikipedia-style reference list -->
        <div class="space-y-3">
            @php
                $referenceNumber = 1;
            @endphp

            @foreach ($resources as $url => $label)
                <div class="flex items-start space-x-3 text-sm">
                    <!-- Reference number -->
                    <span class="min-w-[2rem] flex-shrink-0 font-mono font-medium text-blue-600">
                        [{{ $referenceNumber }}]
                    </span>

                    <!-- Reference content -->
                    <div class="flex-1">
                        <a
                            href="{{ $url }}"
                            target="_blank"
                            class="break-all text-blue-600 hover:text-blue-800 hover:underline"
                        >
                            {{ parse_url($url, PHP_URL_HOST) ? Str::ltrim(parse_url($url, PHP_URL_HOST), 'www.') : $url }}
                        </a>
                        <span class="ml-1 text-gray-500">- {{ $url }}</span>

                        <!-- External link icon -->
                        <svg
                            class="ml-1 inline h-3 w-3 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                            ></path>
                        </svg>
                    </div>
                </div>
                @php
                    $referenceNumber++;
                @endphp
            @endforeach
        </div>

        <!-- Optional: Add a note about references -->
        <div class="mt-6 border-t border-gray-200 pt-4">
            <p class="text-xs italic text-gray-500">
                External links are provided for reference and verification purposes.
            </p>
        </div>
    </div>
@endif
