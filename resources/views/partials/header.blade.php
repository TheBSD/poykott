@if (config('app.env') == 'local')
    <header class="sticky top-0 z-50 bg-green-500 p-1 text-center font-bold text-white">LOCAL DEV</header>
@endif

<div class="min-h-[theme(spacing.12)]">
    <div class="relative bg-white" x-cloak x-data="{ open: false }" @keydown.escape.window="open = false">
        <div class="container mx-auto px-4 sm:px-6">
            <div
                class="flex items-center justify-between border-b-2 border-gray-100 py-6 md:justify-start md:space-x-10"
            >
                <div class="flex justify-start lg:w-0 lg:flex-1">
                    <a href="{{ route('companies.index') }}">
                        <span class="sr-only">Workflow</span>
                        <img class="h-[75px] w-auto" src="{{ asset('images/logo.png') }}" alt="" />
                    </a>
                </div>
                <div class="-my-2 -mr-2 md:hidden">
                    <button
                        type="button"
                        class="inline-flex items-center justify-center rounded-md bg-white p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                        @click="open = true"
                        aria-expanded="false"
                        :aria-expanded="open.toString()"
                    >
                        <span class="sr-only">Open menu</span>
                        <svg
                            class="h-6 w-6"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            ></path>
                        </svg>
                    </button>
                </div>
                <nav class="hidden space-x-10 md:flex">
                    <a
                        href="{{ route('companies.index') }}"
                        class="{{ request()->routeIs('companies.index') ? 'text-gray-900' : 'text-gray-500' }} text-base font-medium hover:text-gray-900"
                    >
                        Israeli Companies
                    </a>
                    <a
                        href="{{ route('alternatives.index') }}"
                        class="{{ request()->routeIs('alternatives.index') ? 'text-gray-900' : 'text-gray-500' }} text-base font-medium hover:text-gray-900"
                    >
                        Alternatives
                    </a>
                    <a
                        href="{{ route('similar-sites') }}"
                        class="{{ request()->routeIs('similar-sites') ? 'text-gray-900' : 'text-gray-500' }} text-base font-medium hover:text-gray-900"
                    >
                        Similar Sites
                    </a>

                    <a
                        href="{{ route('newsletter.get') }}"
                        class="{{ request()->routeIs('newsletter.get') ? 'text-gray-900' : 'text-gray-500' }} text-base font-medium hover:text-gray-900"
                    >
                        Stay updated
                    </a>
                    <a
                        href="{{ route('matrix.index') }}"
                        class="{{ request()->routeIs('matrix.*') ? 'text-gray-900' : 'text-gray-500' }} text-base font-medium hover:text-gray-900"
                    >
                        Matrix-Alternatives
                    </a>
                </nav>
            </div>
        </div>

        <!-- Mobile menu -->
        <div
            x-show="open"
            x-transition:enter="duration-200 ease-out"
            x-transition:enter-start="scale-95 opacity-0"
            x-transition:enter-end="scale-100 opacity-100"
            x-transition:leave="duration-100 ease-in"
            x-transition:leave-start="scale-100 opacity-100"
            x-transition:leave-end="scale-95 opacity-0"
            class="absolute inset-x-0 top-0 z-50 origin-top-right transform p-2 transition md:hidden"
            style="display: none"
            @click.away="open = false"
        >
            <div class="divide-y-2 divide-gray-50 rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5">
                <div class="px-5 pb-6 pt-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <img class="h-[75px] w-auto" src="{{ asset('images/logo.png') }}" alt="Logo" />
                        </div>
                        <div class="-mr-2">
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-md bg-white p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                @click="open = false"
                            >
                                <span class="sr-only">Close menu</span>
                                <svg
                                    class="h-6 w-6"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    ></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="mt-6">
                        <nav class="grid gap-y-8">
                            <a
                                href="{{ route('companies.index') }}"
                                @click="open = false"
                                class="{{ request()->routeIs('companies.index') ? 'bg-blue-50' : '' }} -m-3 flex items-center rounded-md p-3 hover:bg-gray-50"
                            >
                                <svg
                                    class="h-6 w-6 flex-shrink-0 text-indigo-600"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"
                                    />
                                </svg>
                                <span class="ml-3 text-base font-medium text-gray-900">Israeli Companies</span>
                            </a>

                            <a
                                href="{{ route('alternatives.index') }}"
                                @click="open = false"
                                class="{{ request()->routeIs('alternatives.index') ? 'bg-blue-50' : '' }} -m-3 flex items-center rounded-md p-3 hover:bg-gray-50"
                            >
                                <svg
                                    class="h-6 w-6 flex-shrink-0 text-indigo-600"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                    />
                                </svg>
                                <span class="ml-3 text-base font-medium text-gray-900">Alternatives</span>
                            </a>

                            <a
                                href="{{ route('similar-sites') }}"
                                @click="open = false"
                                class="{{ request()->routeIs('similar-sites') ? 'bg-blue-50' : '' }} -m-3 flex items-center rounded-md p-3 hover:bg-gray-50"
                            >
                                <svg
                                    class="h-6 w-6 flex-shrink-0 text-indigo-600"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                                    ></path>
                                </svg>
                                <span class="ml-3 text-base font-medium text-gray-900">Similar Sites</span>
                            </a>

                            <a
                                href="{{ route('newsletter.get') }}"
                                @click="open = false"
                                class="{{ request()->routeIs('newsletter.get') ? 'bg-blue-50' : '' }} -m-3 flex items-center rounded-md p-3 hover:bg-gray-50"
                            >
                                <svg
                                    class="h-6 w-6 flex-shrink-0 text-indigo-600"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                                    ></path>
                                </svg>
                                <span class="ml-3 text-base font-medium text-gray-900">Stay updated</span>
                            </a>

                            <a
                                href="{{ route('matrix.index') }}"
                                @click="open = false"
                                class="{{ request()->routeIs('matrix.*') ? 'bg-blue-50' : '' }} -m-3 flex items-center rounded-md p-3 hover:bg-gray-50"
                            >
                                <svg
                                    class="h-6 w-6 flex-shrink-0 text-indigo-600"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                </svg>
                                <span class="ml-3 text-base font-medium text-gray-900">Matrix-Alternatives</span>
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
