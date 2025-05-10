<x-app-layout>
    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <x-breadcrumb
                :items="[
                    ['name' => 'Home', 'url' => '/'],
                    ['name' => 'Alternatives', 'url' => '/'],
                    ['name' => $alternative->name, 'url' => '/alternatives/' . $alternative->id],
                ]"
                class="mb-6"
            />

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <!-- Company Header -->
                <div class="flex flex-col items-start gap-6 p-8 sm:flex-row">
                    <div class="flex-shrink-0">
                        <img
                            loading="lazy"
                            src="{{ $alternative->image_path }}"
                            alt="{{ $alternative->name }}"
                            class="h-32 w-32 rounded-lg object-contain shadow-md sm:h-40 sm:w-40"
                        />
                    </div>

                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $alternative->name }}</h1>
                        <p class="mt-3 text-lg text-gray-600">{{ $alternative->description }}</p>

                        <!-- Tags -->
                        @if ($alternative->tagsRelation->count() > 0)
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($alternative->tagsRelation as $tag)
                                    <span
                                        class="rounded-full border border-blue-200 bg-blue-100 px-3 py-1 text-sm font-medium text-blue-700 shadow-sm dark:border-blue-800 dark:bg-blue-900 dark:text-blue-200"
                                    >
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Main Content Sections -->
                <div class="space-y-8 divide-y divide-gray-200 px-8 py-6">
                    <!-- Alternatives Section -->
                    <div class="pt-6">
                        <h2 class="mb-6 text-2xl font-bold text-gray-900">Alternatives to</h2>

                        @if ($alternative->companies->isNotEmpty())
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                                @foreach ($alternative->companies as $company)
                                    <div
                                        class="relative overflow-hidden rounded-xl border border-gray-200 shadow-sm transition-all hover:shadow-md"
                                    >
                                        <!-- Boycott Badge -->
                                        <div
                                            class="absolute right-2 top-2 z-10 flex items-center rounded-full bg-red-600 px-2 py-1 text-xs font-bold text-white"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="mr-1 h-3 w-3"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                            BOYCOTT
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
                                            <h3 class="mb-2 text-lg font-semibold text-gray-900">
                                                {{ $company->name }}
                                            </h3>
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
                        @else
                            <p class="text-gray-500">No alternatives listed yet.</p>
                        @endif
                    </div>

                    <!-- Resources -->
                    @php
                        // Filter out resources with '#' URLs and check if any valid resources remain
                        $validResources = $alternative->resources->filter(fn ($resource) => $resource->url !== '#');
                    @endphp

                    @if ($validResources->isNotEmpty())
                        <div class="pt-8">
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
    </div>
</x-app-layout>
