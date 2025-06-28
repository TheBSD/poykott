<div>
    <!-- Searching/Filtering box -->
    <section>
        <div
            class="mb-6 rounded-lg border border-slate-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
        >
            <div class="flex w-full flex-col gap-4 sm:flex-row sm:gap-2">
                <!-- Search input -->
                <input
                    wire:model.live="search"
                    type="text"
                    placeholder="Search..."
                    class="w-full rounded-md border px-4 py-2 focus:border-blue-300 focus:outline-none focus:ring sm:w-5/6"
                />

                <!-- Order select -->
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
            </div>
        </div>
    </section>

    <!-- Companies grid -->
    <section>
        <div id="company-list" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse ($investors as $investor)
                <a href="{{ route('investors.show', $investor) }}" class="group">
                    <div
                        class="relative flex h-full cursor-pointer flex-col overflow-hidden rounded-lg bg-white shadow-sm transition-all duration-300 hover:shadow-md dark:border dark:border-gray-700 dark:bg-gray-800"
                    >
                        <!-- Boycott Badge -->
                        <div
                            class="absolute right-2 top-2 z-10 flex items-center rounded-full bg-red-600 px-2 py-1 text-xs font-bold text-white"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="mr-1 h-3 w-3"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            BOYCOTT
                        </div>

                        <div
                            class="relative aspect-[4/3] w-full overflow-hidden bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-800"
                        >
                            <div class="flex h-full w-full items-center justify-center">
                                <img
                                    src="{{ $investor->image_path }}"
                                    alt="{{ $investor->name }} logo"
                                    class="h-[70%] w-[70%] object-contain opacity-90 transition-opacity group-hover:opacity-100"
                                    loading="lazy"
                                />
                            </div>
                        </div>
                        <div class="flex w-full flex-grow flex-col justify-between p-4">
                            <div>
                                <h3 class="mb-2 line-clamp-1 text-lg font-bold dark:text-white">
                                    {{ $investor->name }}
                                </h3>
                            </div>
                            <div class="flex flex-col gap-2">
                                <button
                                    class="flex w-full items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-800 transition-colors hover:bg-gray-50 hover:text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
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
                <div class="col-span-full py-10 text-center text-xl text-gray-500 dark:text-gray-400">
                    No investors found. Try changing your search or filter.
                </div>
            @endforelse
        </div>
        <div class="mt-2">
            {{ $investors->links() }}
        </div>
    </section>
</div>
