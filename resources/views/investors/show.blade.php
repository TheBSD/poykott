<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <x-breadcrumb
                :items="[
                    ['name' => 'Home', 'url' => '/'],
                    ['name' => 'Investors', 'url' => ''],
                    ['name' => $investor->name, 'url' => '/investors/' . $investor->id],
                ]"
            />

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <!-- Investor Header -->
                <div class="flex items-center space-x-6 p-6">
                    @if ($investor->getFirstMediaUrl())
                        <img
                            src="{{ $investor->getFirstMediaUrl() }}"
                            alt="avatar"
                            class="h-40 w-40 rounded-lg object-cover"
                        />
                    @endif

                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $investor->name }}</h1>
                        <p class="mt-2 text-gray-600">{{ $investor->description }}</p>
                    </div>
                </div>

                <!-- Companies -->
                @if ($investor->companies->count() > 0)
                    <!-- Alternatives Section -->
                    <div class="p-8 pt-6">
                        <h2 class="mb-6 text-2xl font-bold text-gray-900">Companies</h2>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                            @foreach ($investor->companies as $company)
                                <a
                                    href="{{ route('companies.show', $company->slug) }}"
                                    class="relative overflow-hidden rounded-xl border border-gray-200 shadow-sm transition-all hover:shadow-md"
                                >
                                    <div class="">
                                        <!-- Boycott Badge with tooltip -->
                                        <div
                                            x-data="{
                                                tooltipVisible: false,
                                                tooltipText: 'Israeli Company',
                                                tooltipArrow: true,
                                                tooltipPosition: 'left',
                                            }"
                                            x-init="
                                                $refs.content.addEventListener('mouseenter', () => {
                                                    tooltipVisible = true
                                                })
                                                $refs.content.addEventListener('mouseleave', () => {
                                                    tooltipVisible = false
                                                })
                                            "
                                            class="absolute right-2 top-2 z-20"
                                        >
                                            <div
                                                x-ref="tooltip"
                                                x-show="tooltipVisible"
                                                :class="{ 'top-0 left-1/2 -translate-x-1/2 -mt-0.5 -translate-y-full' : tooltipPosition == 'top', 'top-1/2 -translate-y-1/2 -ml-0.5 left-0 -translate-x-full' : tooltipPosition == 'left', 'bottom-0 left-1/2 -translate-x-1/2 -mb-0.5 translate-y-full' : tooltipPosition == 'bottom', 'top-1/2 -translate-y-1/2 -mr-0.5 right-0 translate-x-full' : tooltipPosition == 'right' }"
                                                class="absolute w-auto text-sm"
                                                x-cloak
                                            >
                                                <div
                                                    x-show="tooltipVisible"
                                                    x-transition
                                                    class="relative rounded bg-black bg-opacity-90 px-2 py-1 text-white shadow-lg"
                                                >
                                                    <p
                                                        x-text="tooltipText"
                                                        class="block flex-shrink-0 whitespace-nowrap text-xs"
                                                    ></p>
                                                    <div
                                                        x-ref="tooltipArrow"
                                                        x-show="tooltipArrow"
                                                        :class="{ 'bottom-0 -translate-x-1/2 left-1/2 w-2.5 translate-y-full' : tooltipPosition == 'top', 'right-0 -translate-y-1/2 top-1/2 h-2.5 -mt-px translate-x-full' : tooltipPosition == 'left', 'top-0 -translate-x-1/2 left-1/2 w-2.5 -translate-y-full' : tooltipPosition == 'bottom', 'left-0 -translate-y-1/2 top-1/2 h-2.5 -mt-px -translate-x-full' : tooltipPosition == 'right' }"
                                                        class="absolute inline-flex items-center justify-center overflow-hidden"
                                                    >
                                                        <div
                                                            :class="{ 'origin-top-left -rotate-45' : tooltipPosition == 'top', 'origin-top-left rotate-45' : tooltipPosition == 'left', 'origin-bottom-left rotate-45' : tooltipPosition == 'bottom', 'origin-top-right -rotate-45' : tooltipPosition == 'right' }"
                                                            class="h-1.5 w-1.5 transform bg-black bg-opacity-90"
                                                        ></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                x-ref="content"
                                                class="relative flex cursor-pointer items-center rounded-full bg-red-600 px-2 py-1 text-xs font-bold text-white"
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
                                        </div>
                                        <!-- ./Boycott Badge with tooltip -->
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
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Resources -->
                <x-resources :resources="$resources" :class="'p-6 pt-8'" />
            </div>
        </div>
    </div>
</x-app-layout>
