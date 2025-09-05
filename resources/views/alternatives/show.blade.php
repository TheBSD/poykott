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
                        <div class="flex items-center gap-3">
                            <h1 class="text-3xl font-bold text-gray-900">{{ $alternative->name }}</h1>
                            @if ($alternative->url)
                                <a
                                    href="{{ Str::start($alternative->url, 'https://') }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    title="Visit website"
                                    class="text-blue-500 hover:text-blue-700"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-7 w-7"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                                        />
                                    </svg>
                                </a>
                            @endif
                        </div>
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
                                            <h3 class="mb-2 text-lg font-semibold text-gray-900">
                                                {{ $company->name }}
                                            </h3>
                                            <p class="mb-4 flex-1 text-gray-600">
                                                {{ Str::limit($company->description, 120) }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">No alternatives listed yet.</p>
                        @endif
                    </div>

                    <!-- Resources -->
                    <x-resources :resources="$resources" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
