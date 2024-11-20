<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <x-breadcrumb :items="[
                ['name' => 'Home', 'url' => '/'],
                ['name' => 'People', 'url' => '/people'],
                ['name' => $person->name, 'url' => '/people/' . $person->id],
            ]" />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Person Header -->
                <div class="p-6 flex items-center space-x-6">
                    @if ($person->avatar)
                        <img src="{{ $person->avatar }}" alt="avatar" class="w-40 h-40 object-cover rounded-lg">
                    @endif
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $person->name }}</h1>
                        <p class="mt-2 text-gray-600">{{ $person->description }}</p>
                    </div>
                </div>

                <!-- Companies -->
                @if ($person->companies->count() > 0)
                    <div class="p-6 border-t">
                        <h2 class="text-xl font-semibold mb-4">Companies</h2>
                        <div class="flex flex-wrap gap-4">
                            @foreach ($person->companies as $company)
                                <div class="border rounded-lg p-4 flex items-center flex-col">
                                    @if ($company->logo)
                                        <img src="{{ $company->logo->path }}" alt="{{ $company->name }}"
                                            class="w-40 h-30 rounded-full">
                                    @endif
                                    <a href="{{ route('companies.show', $company) }}"
                                        class="font-medium text-blue-500 hover:underline">{{ $company->name }}</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Resources -->
                @if ($person->resources->count() > 0)
                    <div class="p-6 border-t">
                        <h2 class="text-xl font-semibold mb-4">Resources</h2>
                        <div class="grid gap-4">
                            @foreach ($person->resources as $resource)
                                <div class="border rounded-lg p-4 break-words">
                                    <a href="{{ $resource->url }}"
                                        class="font-medium text-blue-500 hover:underline break-all">{{ $resource->url }}</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
