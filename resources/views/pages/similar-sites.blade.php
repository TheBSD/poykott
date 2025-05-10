<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <div class="container mx-auto px-4 py-16">
            <div class="mx-auto mb-16 max-w-4xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <!-- Main Heading -->
                    <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl md:text-6xl">
                        <span class="block">Similar Sites</span>
                        <span class="mt-2 block text-xl font-semibold text-blue-500 sm:text-2xl">
                            Standing Together for Palestine
                        </span>
                    </h1>

                    <!-- Description with improved styling -->
                    <div class="mx-auto mt-8 max-w-3xl">
                        <p
                            class="rounded-xl border border-gray-200 bg-gray-50/50 px-6 py-5 text-lg leading-8 text-gray-600 shadow-sm"
                        >
                            Here we focus on boycotting Israeli tech. Many allied organizations work on
                            <span class="font-medium text-gray-800">boycotting goods that support Israel</span>
                            ,
                            <span class="font-medium text-gray-800">educating about Palestinian rights</span>
                            , and
                            <span class="font-medium text-gray-800">supporting Palestine</span>
                            . While we share common goals, we are not responsible for their content.
                        </p>
                    </div>

                    <!-- Decorative elements (optional) -->
                    <div class="mt-8 flex justify-center space-x-4">
                        <div class="h-1.5 w-12 rounded-full bg-blue-600"></div>
                        <div class="h-1.5 w-6 rounded-full bg-blue-400"></div>
                        <div class="h-1.5 w-3 rounded-full bg-blue-300"></div>
                    </div>
                </div>
            </div>

            <!-- Sites List Section -->
            <div class="mx-auto max-w-7xl">
                <div class="rounded-2xl bg-white p-6 shadow-xl md:p-8">
                    <div class="space-y-12">
                        @foreach ($similarSitesCategories as $category)
                            <div class="category-group mb-12 last:mb-0">
                                <div class="mb-8">
                                    <h3
                                        class="text-2xl font-bold leading-tight tracking-tight text-gray-900 md:text-3xl lg:text-[2rem] lg:leading-[1.2]"
                                    >
                                        #{{ $category->name }}
                                    </h3>
                                    @if ($category->description)
                                        <p
                                            class="mt-3 max-w-3xl text-base leading-relaxed text-gray-600 md:text-lg md:leading-7"
                                        >
                                            {{ $category->description }}
                                        </p>
                                    @endif
                                </div>
                                <!-- new changes -->
                                @if ($category->similarSites()->exists())
                                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                                        @foreach ($category->similarSites as $site)
                                            <a
                                                href="{{ $site->url }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-all hover:-translate-y-1 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                            >
                                                @if ($site->image_path)
                                                    <div class="flex h-52 items-center overflow-hidden bg-gray-50">
                                                        <img
                                                            src="{{ $site->image_path }}"
                                                            alt="{{ $site->name }} thumbnail"
                                                            class="h-full w-full object-contain p-4 transition-transform duration-300 group-hover:scale-105"
                                                            loading="lazy"
                                                        />
                                                    </div>
                                                @endif

                                                <div class="flex-grow p-5">
                                                    <h3
                                                        class="line-clamp-2 text-lg font-bold text-gray-800 transition-colors group-hover:text-blue-600"
                                                    >
                                                        {{ $site->name }}
                                                    </h3>
                                                    @if ($site->description)
                                                        <p
                                                            class="line-clamp-7 mt-2.5 text-sm leading-normal text-gray-600 md:text-[0.9375rem] md:leading-6"
                                                        >
                                                            {{ $site->description }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
