<x-app-layout>
    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <x-breadcrumb
                :items="[
                    ['name' => 'Home', 'url' => '/'],
                    ['name' => 'Israeli Companies', 'url' => '/companies'],
                    ['name' => $company->name, 'url' => '/companies/' . $company->id],
                ]"
                class="mb-6"
            />

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <!-- Company Header -->
                <div class="flex flex-col items-start gap-6 p-8 sm:flex-row">
                    <div class="flex-shrink-0">
                        <img
                            loading="lazy"
                            src="{{ $company->image_path }}"
                            alt="{{ $company->name }}"
                            class="h-32 w-32 rounded-lg object-contain shadow-md sm:h-40 sm:w-40"
                        />
                    </div>

                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900">
                            {{ $company->name }}
                            <span
                                class="inline-flex items-center rounded-full border border-amber-200 bg-red-600 px-2 py-0.5 text-xs font-semibold text-white shadow-sm"
                            >
                                <svg
                                    class="size-4"
                                    x-description="Heroicon name: outline/no-symbol"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"
                                    />
                                </svg>
                                Israeli Company
                            </span>
                        </h1>

                        <p class="mt-3 text-lg text-gray-600">{{ $company->description }}</p>

                        <!-- Tags -->
                        @if ($company->tagsRelation->count() > 0)
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($company->tagsRelation as $tag)
                                    <span
                                        class="rounded-full border border-blue-200 bg-blue-100 px-3 py-1 text-sm font-medium text-blue-700 shadow-sm"
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
                        <h2 class="mb-6 text-2xl font-bold text-gray-900">Alternatives</h2>

                        @if ($company->alternatives->count() > 0)
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                                @foreach ($company->alternatives as $alternative)
                                    <div
                                        class="overflow-hidden rounded-xl border border-gray-200 shadow-sm transition-all hover:shadow-md"
                                    >
                                        <div class="flex h-full flex-col p-5">
                                            <div class="mb-4 flex justify-center">
                                                <img
                                                    loading="lazy"
                                                    src="{{ $alternative->image_path }}"
                                                    alt="{{ $alternative->name }}"
                                                    class="h-20 w-20 rounded-lg object-contain"
                                                />
                                            </div>
                                            <h3 class="mb-2 text-lg font-semibold text-gray-900">
                                                {{ $alternative->name }}
                                            </h3>
                                            <p class="mb-4 flex-1 text-gray-600">
                                                {{ Str::limit($alternative->description, 120) }}
                                            </p>
                                            @if ($alternative->url && $alternative->url !== '#')
                                                <a
                                                    href="{{ $alternative->url }}"
                                                    target="_blank"
                                                    class="inline-block truncate text-blue-600 hover:text-blue-800 hover:underline"
                                                    title="{{ $alternative->url }}"
                                                >
                                                    {{ parse_url($alternative->url, PHP_URL_HOST) }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">No alternatives listed yet.</p>
                        @endif

                        <!-- Add Alternative Form-- -->
                        <div class="mt-10 rounded-xl bg-gray-50 p-6">
                            <h3 class="mb-4 text-xl font-semibold text-gray-900">Suggest an Alternative</h3>
                            <form
                                action="{{ route('companies.alternatives.store', $company) }}"
                                method="POST"
                                class="space-y-4"
                            >
                                @csrf
                                <x-honeypot />

                                <!-- Validation Errors -->
                                @if ($errors->any())
                                    <div class="rounded-md bg-red-50 p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg
                                                    class="h-5 w-5 text-red-400"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-red-800">
                                                    There {{ $errors->count() > 1 ? 'were' : 'was' }}
                                                    {{ $errors->count() }}
                                                    {{ Str::plural('error', $errors->count()) }} with your submission
                                                </h3>
                                                <div class="mt-2 text-sm text-red-700">
                                                    <ul class="list-disc space-y-1 pl-5">
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="flex flex-col gap-4 md:flex-row md:items-end">
                                    <div class="w-full flex-1">
                                        <label for="name" class="mb-1 block text-sm font-medium text-gray-700">
                                            Name
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            name="name"
                                            id="name"
                                            class="w-full rounded-md border p-2"
                                            required
                                        />
                                    </div>

                                    <div class="w-full md:w-2/4">
                                        <label for="url" class="block text-sm font-medium text-gray-700">
                                            URL
                                            <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="url"
                                            name="url"
                                            id="url"
                                            class="w-full rounded-md border p-2"
                                            required
                                        />
                                    </div>

                                    <div class="mt-4 w-full md:w-1/4">
                                        <button
                                            type="submit"
                                            class="rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                        >
                                            Submit Alternative
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Company Details Sections -->
                    <div class="grid grid-cols-1 gap-6 pt-8 md:grid-cols-2">
                        <!-- Founders -->
                        @if ($company->founders->count() > 0)
                            <div class="rounded-xl border border-gray-200 p-6">
                                <h2 class="mb-4 text-xl font-semibold text-gray-900">Founders</h2>
                                <div class="space-y-4">
                                    @foreach ($company->founders as $founder)
                                        <div class="flex items-center gap-4">
                                            <img
                                                loading="lazy"
                                                src="{{ $founder->image_path }}"
                                                alt="avatar"
                                                class="h-16 w-16 rounded-full object-cover"
                                            />
                                            <div>
                                                <a
                                                    href="{{ route('people.show', $founder->slug) }}"
                                                    class="text-lg font-medium text-gray-900 hover:text-blue-600"
                                                >
                                                    {{ $founder->name }}
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Investors -->
                        @if ($company->investors->count() > 0)
                            <div class="rounded-xl border border-gray-200 p-6">
                                <h2 class="mb-4 text-xl font-semibold text-gray-900">Investors</h2>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    @foreach ($company->investors as $investor)
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100"
                                            >
                                                <svg
                                                    class="h-5 w-5 text-gray-500"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                    ></path>
                                                </svg>
                                            </div>
                                            <a href="{{ route('investors.show', $investor) }}">
                                                <span class="text-gray-700 underline">{{ $investor->name }}</span>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Office Locations -->
                    @if ($company->officeLocations->count() > 0)
                        <div class="rounded-xl border border-gray-200 p-6">
                            <h2 class="mb-4 text-xl font-semibold text-gray-900">Office Locations</h2>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($company->officeLocations as $location)
                                    <div class="flex items-start">
                                        <svg
                                            class="mr-2 mt-1 h-5 w-5 flex-shrink-0 text-gray-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                            ></path>
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                            ></path>
                                        </svg>
                                        <span class="text-gray-700">{{ $location->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Resources -->
                    @if ($company->resources->count() > 0)
                        <div class="pt-8">
                            <h2 class="mb-6 text-2xl font-bold text-gray-900">References</h2>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                                @foreach ($resources as $url => $label)
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
