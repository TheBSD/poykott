<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <x-breadcrumb :items="[
                ['name' => 'Home', 'url' => '/'],
                ['name' => 'Companies', 'url' => '/'],
                ['name' => $company->name, 'url' => '/companies/' . $company->id],
            ]" />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Company Header -->
                <div class="p-6 flex items-center space-x-6">
                    @if ($company->logo)
                        <img src="{{ $company->logo->path }}" alt="{{ $company->name }}"
                            class="w-24 h-24 object-cover rounded-lg">
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
                                <span class="px-3 py-1 bg-amber-500 rounded-full text-sm">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                    <!-- Founders -->
                    @if ($company->founders->count() > 0)
                        <div class="border rounded-lg p-4">
                            <h2 class="text-xl font-semibold mb-4">Founders</h2>
                            <div class="space-y-4">
                                @foreach ($company->founders as $founder)
                                    <div class="flex items-center space-x-3">
                                        @if ($founder->avatar)
                                            <img src="{{ $founder->avatar }}" alt="avatar"
                                                class="w-24 h-24 rounded-full">
                                        @endif
                                        <div>
                                            <a href="{{ route('people.show', $founder->id) }}"
                                                class="font-medium hover:text-blue-500 ">
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
                        <div class="border rounded-lg p-4">
                            <h2 class="text-xl font-semibold mb-4">Office Locations</h2>
                            <div class="space-y-2">
                                @foreach ($company->officeLocations as $location)
                                    <p>{{ $location->name }}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Investors -->
                    @if ($company->investors->count() > 0)
                        <div class="border rounded-lg p-4">
                            <h2 class="text-xl font-semibold mb-4">Investors</h2>
                            <div class="grid grid-cols-2 gap-4">
                                @foreach ($company->investors as $investor)
                                    <div class="flex items-center space-x-3">
                                        @if ($investor->logo)
                                            <img src="{{ $investor->logo->path }}" alt="{{ $investor->name }}"
                                                class="w-8 h-8 object-cover rounded">
                                        @endif
                                        <span>{{ $investor->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Alternatives -->
                <div class="max-w-7xl sm-px-6 lg:px-8 py-6 border-t">
                    <h2 class="text-xl font-semibold mb-4">Alternatives</h2>
                    @if ($company->alternatives->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($company->alternatives as $alternative)
                                <div class="border rounded-lg p-4">
                                    <h3 class="font-medium">{{ $alternative->name }}</h3>
                                    <p class="text-gray-600">{{ $alternative->description }}</p>
                                    @if ($alternative->url && $alternative->url !== '#')
                                        <a href="{{ $alternative->url }}"
                                            class="text-blue-500 hover:underline break-all">{{ $alternative->url }}</a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Add Alternative -->
                    <div class="mt-8 border-t pt-6 border border-orange-300 p-4 rounded-md">
                        <h3 class="text-lg font-medium mb-4">Suggest an Alternative</h3>
                        <form action="{{ route('companies.alternatives.store', $company) }}" method="POST"
                            class="space-y-4">
                            @csrf

                            {{-- Honeypot field --}}
                            <div class="hidden">
                                <input type="text" name="website" tabindex="-1" autocomplete="off">
                            </div>

                            <div class="flex flex-col md:flex-row gap-4 rounded-md items-center">
                                <div class="w-full md:w-1/4">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                    <input type="text" name="name" id="name"
                                        class="p-2 w-full rounded-md border" required>
                                </div>

                                <div class="w-full md:w-2/4">
                                    <label for="url" class="block text-sm font-medium text-gray-700">
                                        URL</label>
                                    <input type="url" name="url" id="url"
                                        class="p-2 w-full rounded-md border" required>
                                </div>

                                <div class="mt-4 w-full md:w-1/4">
                                    <button type="submit"
                                        class="rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        Submit Alternative
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>

                </div>

                <!-- Resources -->
                @if ($company->resources->count() > 0)
                    <div class="p-6 border-t">
                        <h2 class="text-xl font-semibold mb-4">Resources</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($company->resources as $resource)
                                <div class="border rounded-lg p-4">
                                    <a href="{{ $resource->url }}"
                                        class="font-medium text-blue-500 hover:underline">{{ $resource->url }}</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

