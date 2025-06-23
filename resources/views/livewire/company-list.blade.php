<div>
    <!-- Searching/Filtering box -->
    <section>
        <div class="mb-2 flex justify-end">
            <a
                href="{{ route('companies.create') }}"
                class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
            >
                + Add New Company
            </a>
        </div>
        <div class="mb-6 rounded-lg border border-slate-200 bg-white p-4">
            <div class="flex w-full flex-col gap-4 sm:flex-row sm:gap-2">
                <!-- Search input -->
                <label for="search-input" class="sr-only">Search...</label>
                <input
                    id="search-input"
                    wire:model.live="search"
                    type="text"
                    placeholder="Search..."
                    class="w-full rounded-md border px-4 py-2 focus:border-blue-300 focus:outline-none focus:ring"
                />

                <!-- Filter select -->
                {{--
                    <label for="filter-select" class="sr-only">Filter by</label>
                    <select
                    id="filter-select"
                    wire:model.live="filter"
                    class="w-full rounded-md border px-4 py-2 focus:border-blue-300 focus:outline-none focus:ring sm:w-1/6"
                    >
                    <option value="">Filter by</option>
                    @foreach ($this->companiesTags as $tag)
                    <option value="{{ $tag->id }}">{{ $tag->name }} ({{ $tag->companies_count }})</option>
                    @endforeach
                    </select>
                --}}

                <!-- Order select -->
                {{--
                    <label for="order-select" class="sr-only">Order by</label>
                    <select
                    id="order-select"
                    wire:model.live="order"
                    class="w-full rounded-md border px-4 py-2 focus:border-blue-300 focus:outline-none focus:ring sm:w-1/6"
                    >
                    <option value="">Order by</option>
                    <option value="asc">Ascending</option>
                    <option value="desc">Descending</option>
                    </select>
                --}}
            </div>
        </div>
    </section>

    <!-- Companies grid -->
    <section>
        <div id="company-list" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse ($companies as $company)
                <a href="{{ route('companies.show', $company) }}">
                    <div
                        class="relative flex h-full cursor-pointer flex-col overflow-hidden rounded-lg bg-white shadow-sm transition-all duration-300 hover:shadow-md"
                    >
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
                        <div
                            class="relative aspect-[4/3] w-full overflow-hidden bg-gradient-to-br from-blue-50 to-purple-50"
                        >
                            <div class="flex h-full w-full items-center justify-center">
                                <img
                                    src="{{ $company->image_path }}"
                                    alt="{{ $company->name }} logo"
                                    class="h-[70%] w-[70%] object-contain"
                                    loading="lazy"
                                />
                            </div>
                        </div>
                        <div class="flex w-full flex-grow flex-col justify-between p-4">
                            <div>
                                <div class="mb-3 flex flex-wrap gap-1.5">
                                    @foreach ($company->tagsRelation as $tag)
                                        <span
                                            class="inline-block rounded-full border border-blue-200 bg-blue-100 px-3 py-1 text-xs text-blue-700 shadow-sm"
                                        >
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                                <h3 class="mb-2 line-clamp-1 text-lg font-bold">
                                    {{ $company->name }}
                                </h3>
                                <p class="mb-2 line-clamp-2 text-sm text-slate-700">
                                    {{ $company->short_description ?? Str::limit($company->description, 100) }}
                                </p>
                            </div>
                            <div class="flex flex-col gap-2">
                                <button
                                    class="flex w-full items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-800 transition-colors hover:bg-gray-50 hover:text-gray-900"
                                >
                                    <span class="mr-2">Details</span>

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="24"
                                        height="24"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-info h-4 w-4"
                                    >
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="M12 16v-4"></path>
                                        <path d="M12 8h.01"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-10 text-center text-xl text-gray-500">
                    No companies found. Try changing your search or filter.
                </div>
            @endforelse
        </div>
        <div class="mt-2">
            {{ $companies->links() }}
        </div>
    </section>
</div>
