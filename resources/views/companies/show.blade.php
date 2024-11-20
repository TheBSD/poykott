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
                    @if ($company->logo)
                        <img
                            src="{{ $company->logo->path }}"
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
                                                href="{{ route('people.show', $founder->id) }}"
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
                                        @if ($investor->logo)
                                            <img
                                                src="{{ $investor->logo->path }}"
                                                alt="{{ $investor->name }}"
                                                class="h-8 w-8 rounded object-cover"
                                            />
                                        @endif

                                        <span>{{ $investor->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Alternatives -->
                    @if ($company->alternatives->count() > 0)
                        <div class="rounded-lg border p-4">
                            <h2 class="mb-4 text-xl font-semibold">Alternatives</h2>
                            <div class="space-y-4">
                                @foreach ($company->alternatives as $alternative)
                                    <div>
                                        <h3 class="font-medium">{{ $alternative->name }}</h3>
                                        <p class="text-gray-600">{{ $alternative->description }}</p>
                                        @if ($alternative->url && $alternative->url !== '#')
                                            <a href="{{ $alternative->url }}" class="text-blue-500 hover:underline">
                                                {{ $alternative->url }}
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
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
