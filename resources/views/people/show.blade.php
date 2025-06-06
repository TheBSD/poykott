<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <x-breadcrumb
                :items="[
                    ['name' => 'Home', 'url' => '/'],
                    ['name' => 'People', 'url' => ''],
                    ['name' => $person->name, 'url' => '/people/' . $person->id],
                ]"
            />

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <!-- Person Header -->
                <div class="flex items-center space-x-6 p-6">
                    {{-- @if ($person->image_path) --}}
                    <img src="{{ $person->image_path }}" alt="avatar" class="h-40 w-40 rounded-lg object-cover" />
                    {{-- @endif --}}
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $person->name }}</h1>
                        <p class="mt-2 text-gray-600">{{ $person->description }}</p>
                    </div>
                </div>

                <!-- Companies -->
                @if ($person->companies->isNotEmpty())
                    <div class="p-6">
                        <h2 class="mb-4 text-xl font-semibold">Companies</h2>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                            @foreach ($person->companies as $company)
                                <div
                                    class="relative overflow-hidden rounded-xl border border-gray-200 shadow-sm transition-all hover:shadow-md"
                                >
                                    <!-- Boycott Badge -->
                                    <div
                                        class="absolute right-2 top-2 z-10 flex items-center rounded-full bg-red-600 px-2 py-1 text-xs font-bold text-white"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-3 w-3"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </div>
                                    <div class="flex h-full flex-col p-5">
                                        <div class="mb-4 flex justify-center">
                                            <img
                                                loading="lazy"
                                                src="{{ $company->image_path }}"
                                                alt="{{ $company->name }}"
                                                class="h-20 w-20 rounded-lg object-contain"
                                            />
                                        </div>
                                        <h3 class="mb-2 text-lg font-semibold text-gray-900">{{ $company->name }}</h3>
                                        <p class="mb-4 flex-1 text-gray-600">
                                            {{ Str::limit($company->description, 120) }}
                                        </p>
                                        @if ($company->url && $company->url !== '#')
                                            <a
                                                href="{{ $company->url }}"
                                                target="_blank"
                                                class="inline-block truncate text-blue-600 hover:text-blue-800 hover:underline"
                                                title="{{ $company->url }}"
                                            >
                                                {{ parse_url($company->url, PHP_URL_HOST) }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="p-6 text-gray-500">No alternatives listed yet.</p>
                @endif

                <!-- Resources -->
                @php
                    // Filter out resources with '#' URLs and check if any valid resources remain
                    $validResources = $person->resources->filter(fn ($resource) => $resource->url !== '#');
                @endphp

                @if ($validResources->isNotEmpty())
                    <div class="p-6 pt-8">
                        <h2 class="mb-6 text-2xl font-bold text-gray-900">References</h2>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach ($resources as $url => $label)
                                @if ($url == '#')
                                    @continue
                                @endif

                                <a
                                    href="{{ $url }}"
                                    target="_blank"
                                    class="flex items-center rounded-lg border border-gray-200 p-4 transition-colors hover:bg-gray-50"
                                >
                                    <svg
                                        class="mr-3 h-5 w-5 flex-shrink-0 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"
                                        ></path>
                                    </svg>
                                    <span class="font-medium text-gray-700 hover:text-blue-600">{{ $label }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
