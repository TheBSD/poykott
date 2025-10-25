<x-app-layout>
    <div class="bg-white">
        <div class="bg-white">
            <main class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-xl py-16 sm:py-24">
                    <div class="text-center">
                        <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">404 error</p>
                        <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
                            This page does not exist.
                        </h1>
                        <p class="mt-2 text-lg text-gray-500">The page you are looking for could not be found.</p>
                    </div>
                    <div class="mt-12">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Popular pages</h2>
                        <ul role="list" class="mt-4 divide-y divide-gray-200 border-b border-t border-gray-200">
                            {{-- Companies --}}
                            <li class="relative flex items-start space-x-4 py-6">
                                <div class="flex-shrink-0">
                                    <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-50">
                                        <svg
                                            class="h-6 w-6 text-indigo-700"
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
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-base font-medium text-gray-900">
                                        <span
                                            class="rounded-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2"
                                        >
                                            <a href="{{ route('companies.index') }}" class="focus:outline-none">
                                                <span class="absolute inset-0" aria-hidden="true"></span>
                                                Israeli Companies
                                            </a>
                                        </span>
                                    </h3>
                                    <p class="text-base text-gray-500">
                                        The Israeli services and companies to boycott.
                                    </p>
                                </div>
                                <div class="flex-shrink-0 self-center">
                                    <svg
                                        class="h-5 w-5 text-gray-400"
                                        x-description="Heroicon name: solid/chevron-right"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                </div>
                            </li>

                            {{-- Alternatives --}}
                            <li class="relative flex items-start space-x-4 py-6">
                                <div class="flex-shrink-0">
                                    <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-50">
                                        <svg
                                            class="h-6 w-6 text-indigo-700"
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
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-base font-medium text-gray-900">
                                        <span
                                            class="rounded-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2"
                                        >
                                            <a href="{{ route('alternatives.index') }}" class="focus:outline-none">
                                                <span class="absolute inset-0" aria-hidden="true"></span>
                                                Alternatives
                                            </a>
                                        </span>
                                    </h3>
                                    <p class="text-base text-gray-500">
                                        The Alternatives services and companies to the Israeli ones.
                                    </p>
                                </div>
                                <div class="flex-shrink-0 self-center">
                                    <svg
                                        class="h-5 w-5 text-gray-400"
                                        x-description="Heroicon name: solid/chevron-right"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                </div>
                            </li>

                            {{-- Similar Sites --}}
                            <li class="relative flex items-start space-x-4 py-6">
                                <div class="flex-shrink-0">
                                    <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-50">
                                        <svg
                                            class="fh-6 w-6 text-indigo-700"
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
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-base font-medium text-gray-900">
                                        <span
                                            class="rounded-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2"
                                        >
                                            <a href="{{ route('similar-sites') }}" class="focus:outline-none">
                                                <span class="absolute inset-0" aria-hidden="true"></span>
                                                Similar Sites
                                            </a>
                                        </span>
                                    </h3>
                                    <p class="text-base text-gray-500">
                                        Our Friend sites the support the Palestinian case.
                                    </p>
                                </div>
                                <div class="flex-shrink-0 self-center">
                                    <svg
                                        class="h-5 w-5 text-gray-400"
                                        x-description="Heroicon name: solid/chevron-right"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                </div>
                            </li>
                        </ul>
                        <div class="mt-8">
                            <a
                                href="{{ route('companies.index') }}"
                                class="text-base font-medium text-indigo-600 hover:text-indigo-500"
                            >
                                Or go back home
                                <span aria-hidden="true"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</x-app-layout>
