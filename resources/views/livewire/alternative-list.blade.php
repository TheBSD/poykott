<div>
    <!-- Searching/Filtering box -->
    <section>
        {{--
            <div class="flex justify-end mb-2">
            <a
            href="#"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium shadow-sm"
            >
            + Add New Alternative
            </a>
            </div>
        --}}
        <div class="mb-6 rounded-lg border border-slate-200 bg-white p-4">
            <div class="flex w-full flex-col gap-4 sm:flex-row sm:gap-2">
                <div class="relative w-full">
                    <label for="search-input" class="sr-only">Search...</label>

                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <!-- Spinner -->
                        <svg
                            wire:loading.delay
                            wire:target="search"
                            class="h-4 w-4 animate-spin text-blue-600"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                        </svg>
                    </div>

                    <input
                        id="search-input"
                        wire:model.live="search"
                        type="text"
                        placeholder="Search..."
                        class="w-full rounded-md border py-2 pl-9 pr-3 focus:border-blue-300 focus:outline-none focus:ring"
                    />
                </div>

                <!-- Filter select -->
                {{--
                    <label for="filter-select" class="sr-only">Filter by</label>
                    <select
                    id="filter-select"
                    wire:model.live="filter"
                    class="w-full rounded-md border px-4 py-2 focus:border-blue-300 focus:outline-none focus:ring sm:w-1/6"
                    >
                    <option value="">Filter by</option>
                    @foreach ($this->alternativesTags as $tag)
                    <option value="{{ $tag->id }}">{{ $tag->name }} ({{ $tag->alternatives_count }})</option>
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
        @if ($matchedCompany)
            <div class="mb-4 rounded-md bg-blue-50 p-4" role="status" aria-live="polite">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg
                            class="h-5 w-5 text-blue-400"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                            focusable="false"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h.01a1 1 0 100-2H10V9z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            <a
                                href="{{ route('companies.show', $matchedCompany) }}"
                                class="font-bold text-blue-900 hover:underline"
                            >
                                {{ $matchedCompany->name }}
                            </a>

                            is an Israeli company. Following are the alternatives.
                        </h3>
                    </div>
                </div>
            </div>
        @endif

        <div id="company-list" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse ($alternatives as $alternative)
                <a href="{{ route('alternatives.show', $alternative) }}">
                    <div
                        class="relative flex h-full cursor-pointer flex-col overflow-hidden rounded-lg bg-white shadow-sm transition-all duration-300 hover:shadow-md"
                    >
                        <!-- Alternative Badge (Top Right) with Tooltip -->
                        <div
                            x-data="{
                                tooltipVisible: false,
                                tooltipText: 'NonIsraeli Alternate',
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
                                class="relative flex cursor-pointer items-center rounded-full bg-green-600 px-2 py-1 text-xs font-bold text-white"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="size-4"
                                    x-description="Heroicon name: solid/check-circle"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                        </div>

                        <!-- Country Badge (Top Left) - Different shape and color -->
                        {{-- todo --}}
                        {{-- This will indicates if the alternative is palestinian, from mena, other countries, Europe, the US, using the Mohammed Azman package --}}

                        {{-- <div --}}
                        {{-- class="absolute top-2 left-2 bg-indigo-600 text-white text-xs font-bold px-3 py-1 rounded-md z-10 flex items-center shadow-sm"> --}}
                        {{-- <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" --}}
                        {{-- fill="currentColor"> --}}
                        {{-- <path fill-rule="evenodd" --}}
                        {{-- d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2H5a1 1 0 010-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" --}}
                        {{-- clip-rule="evenodd"/> --}}
                        {{-- </svg> --}}

                        {{-- {{ 'alternative country' }} --}}
                        {{-- </div> --}}

                        <div
                            class="relative aspect-[4/3] w-full overflow-hidden bg-gradient-to-br from-blue-50 to-purple-50"
                        >
                            <div class="flex h-full w-full items-center justify-center">
                                <img
                                    src="{{ $alternative->image_path }}"
                                    alt="{{ $alternative->name }} logo"
                                    class="h-[70%] w-[70%] object-contain"
                                    loading="lazy"
                                />
                            </div>
                        </div>

                        <div class="flex w-full flex-grow flex-col justify-between p-4">
                            <div>
                                <!-- Tags (rounded full) -->
                                <div class="mb-3 flex flex-wrap gap-1.5">
                                    @foreach ($alternative->tagsRelation as $tag)
                                        <span
                                            class="inline-block rounded-full border border-blue-200 bg-blue-100 px-3 py-1 text-xs text-blue-700 shadow-sm"
                                        >
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>

                                <h3 class="mb-2 line-clamp-1 text-lg font-bold">
                                    {{ $alternative->name }}
                                </h3>
                                <p class="mb-2 line-clamp-2 text-sm text-slate-700">
                                    {{ Str::limit($alternative->description, 100) }}
                                </p>

                                <!-- Alternative to section -->
                                @if ($alternative->companies->isNotEmpty())
                                    <div class="my-4 border-t border-gray-100 pt-3">
                                        <div class="mb-2 flex items-center gap-1.5">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-3.5 w-3.5 text-gray-400"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                            <span class="text-xs font-medium text-gray-500">Alternative to:</span>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($alternative->companies as $company)
                                                    <span
                                                        class="inline-flex items-center rounded border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs text-gray-700 transition-colors hover:bg-gray-100"
                                                    >
                                                        {{ $company->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
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
                    Your search is not in our data as an Israeli company, an alternate company or a category. Please use
                    <a href="{{ route('contact.get') }}" target="_blank" class="text-blue-600 hover:text-blue-700">
                        contact us
                    </a>
                    for suggestions.
                </div>
            @endforelse
        </div>
        <div class="mt-2">
            {{ $alternatives->links() }}
        </div>
    </section>
</div>
