<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <x-breadcrumb
                :items="[
                    ['name' => 'Home', 'url' => '/'],
                    ['name' => 'People', 'url' => '/people'],
                    ['name' => $person->name, 'url' => '/people/' . $person->id],
                ]"
            />

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <!-- Person Header -->
                <div class="flex items-center space-x-6 p-6">
                    @if ($person->media)
                        <img src="{{ $person->getFirstMediaUrl() }}" alt="avatar" class="h-40 w-40 rounded-lg object-cover" />
                    @endif

                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $person->name }}</h1>
                        <p class="mt-2 text-gray-600">{{ $person->description }}</p>
                    </div>
                </div>

                <!-- Companies -->
                @if ($person->companies->count() > 0)
                    <div class="border-t p-6">
                        <h2 class="mb-4 text-xl font-semibold">Companies</h2>
                        <div class="flex flex-wrap gap-4">
                            @foreach ($person->companies as $company)
                                <div class="flex flex-col items-center rounded-lg border p-4">
                                    @if ($company->media)
                                        <img
                                            src="{{ $company->getFirstMediaUrl() }}"
                                            alt="logo"
                                            class="h-30 w-40 rounded-full"
                                        />
                                    @endif

                                    <a
                                        href="{{ route('companies.show', $company) }}"
                                        class="font-medium text-blue-500 hover:underline"
                                    >
                                        {{ $company->name }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Resources -->
                @if ($person->resources->count() > 0)
                    <div class="border-t p-6">
                        <h2 class="mb-4 text-xl font-semibold">Resources</h2>
                        <div class="grid gap-4">
                            @foreach ($person->resources as $resource)
                                <div class="break-words rounded-lg border p-4">
                                    <a
                                        href="{{ $resource->url }}"
                                        class="break-all font-medium text-blue-500 hover:underline"
                                    >
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
