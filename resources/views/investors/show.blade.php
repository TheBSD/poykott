<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <x-breadcrumb
                :items="[
                    ['name' => 'Home', 'url' => '/'],
                    ['name' => 'Investors', 'url' => '/investors'],
                    ['name' => $investor->name, 'url' => '/investors/' . $investor->id],
                ]"
            />

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <!-- Investor Header -->
                <div class="flex items-center space-x-6 p-6">
                    @if ($investor->getFirstMediaUrl())
                        <img src="{{ $investor->getFirstMediaUrl() }}" alt="avatar" class="h-40 w-40 rounded-lg object-cover" />
                    @endif

                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $investor->name }}</h1>
                        <p class="mt-2 text-gray-600">{{ $investor->description }}</p>
                    </div>
                </div>

                <!-- Companies -->
                @if ($investor->companies->count() > 0)
                    <div class="border-t p-6">
                        <h2 class="mb-4 text-xl font-semibold">Companies</h2>
                        <div class="flex flex-wrap gap-4">
                            @foreach ($investor->companies as $company)
                                <div class="flex flex-col items-center rounded-lg border p-4">
                                    @if ($company->getFirstMediaUrl())
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
                @if ($investor->resources->count() > 0)
                    <div class="border-t p-6">
                        <h2 class="mb-4 text-xl font-semibold">Resources</h2>
                        <div class="grid gap-4">
                            @foreach ($investor->resources as $resource)
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
