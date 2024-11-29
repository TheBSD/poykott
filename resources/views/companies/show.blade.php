<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <x-breadcrumb
                :items="[
                    ['name' => 'Home', 'url' => '/'],
                    ['name' => 'Companies', 'url' => '/'],
                    ['name' => $company->name, 'url' => '/companies/' . $company->id],
                ]"
            />

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <!-- Company Header -->
                <div class="flex items-center space-x-6 p-6">
                    @if ($company->media)
                        <img
                            src="{{ $company->getFirstMediaUrl() }}"
                            alt="{{ $company->name }}"
                            class="h-24 w-24 rounded-lg object-cover"
                        />
                    @endif

                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $company->name }}</h1>
                        <p class="mt-2 text-gray-600">{{ $company->description }}</p>
                    </div>
                </div>

                <!-- Tags -->
                @if ($company->tagsRelation->count() > 0)
                    <div class="px-6 pb-4">
                        <div class="flex flex-wrap gap-2">
                            @foreach ($company->tagsRelation as $tag)
                                <span class="rounded-full bg-amber-500 px-3 py-1 text-sm">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">
                    <!-- Founders -->
                    @if ($company->founders->count() > 0)
                        <div class="rounded-lg border p-4">
                            <h2 class="mb-4 text-xl font-semibold">Founders</h2>
                            <div class="space-y-4">
                                @foreach ($company->founders as $founder)
                                    <div class="flex items-center space-x-3">
                                        @if ($founder->avatar)
                                            <img
                                                src="{{ $founder->avatar }}"
                                                alt="avatar"
                                                class="h-24 w-24 rounded-full"
                                            />
                                        @endif

                                        <div>
                                            <a
                                                href="{{ route('people.show', $founder->slug) }}"
                                                class="font-medium hover:text-blue-500"
                                            >
                                                {{ $founder->name }}
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Office Locations -->
                    @if ($company->officeLocations->count() > 0)
                        <div class="rounded-lg border p-4">
                            <h2 class="mb-4 text-xl font-semibold">Office Locations</h2>
                            <div class="space-y-2">
                                @foreach ($company->officeLocations as $location)
                                    <p>{{ $location->name }}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Investors -->
                    @if ($company->investors->count() > 0)
                        <div class="rounded-lg border p-4">
                            <h2 class="mb-4 text-xl font-semibold">Investors</h2>
                            <div class="grid grid-cols-2 gap-4">
                                @foreach ($company->investors as $investor)
                                    <div class="flex items-center space-x-3">
                                        @if ($investor->getFirstMediaUrl())
                                            <img
                                                src="{{ $investor->getFirstMediaUrl() }}"
                                                alt="logo"
                                                class="h-8 w-8 rounded object-cover"
                                            />
                                        @endif

                                        <div>
                                            <a
                                                href="{{ route('investors.show', $investor->slug) }}"
                                                class="font-medium hover:text-blue-500"
                                            >
                                                {{ $investor->name }}
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Alternatives -->
                <div class="sm-px-6 max-w-7xl border-t py-6 lg:px-8">
                    <h2 class="mb-4 text-xl font-semibold">Alternatives</h2>
                    @if ($company->alternatives->count() > 0)
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach ($company->alternatives as $alternative)
                                <div class="rounded-lg border p-4">
                                    <h3 class="font-medium">{{ $alternative->name }}</h3>
                                    <p class="text-gray-600">{{ $alternative->description }}</p>
                                    @if ($alternative->url && $alternative->url !== '#')
                                        <a
                                            href="{{ $alternative->url }}"
                                            class="break-all text-blue-500 hover:underline"
                                        >
                                            {{ $alternative->url }}
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Add Alternative -->
                    <div class="mt-8 rounded-md border border-t border-orange-300 p-4 pt-6">
                        <h3 class="mb-4 text-lg font-medium">Suggest an Alternative</h3>
                        <form
                            action="{{ route('companies.alternatives.store', $company) }}"
                            method="POST"
                            class="space-y-4"
                        >
                            @csrf
                            <x-honeypot />

                            <div class="flex flex-col items-center gap-4 rounded-md md:flex-row">
                                <div class="w-full md:w-1/4">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                    <input
                                        type="text"
                                        name="name"
                                        id="name"
                                        class="w-full rounded-md border p-2"
                                        required
                                    />
                                </div>

                                <div class="w-full md:w-2/4">
                                    <label for="url" class="block text-sm font-medium text-gray-700">URL</label>
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

                <!-- Resources -->
                @if ($company->resources->count() > 0)
                    <div class="border-t p-6">
                        <h2 class="mb-4 text-xl font-semibold">Resources</h2>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach ($company->resources as $resource)
                                <div class="rounded-lg border p-4">
                                    <a href="{{ $resource->url }}" class="font-medium text-blue-500 hover:underline">
                                        {{ $resource->url }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
