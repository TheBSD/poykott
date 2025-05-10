<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <h1 class="mb-12 text-center text-4xl font-bold md:text-5xl">Frequently Asked Questions</h1>

            {{-- Accordion --}}
            <div class="bg-white">
                <div class="mx-auto max-w-7xl rounded-2xl bg-white p-8 px-4 py-12 shadow-xl sm:px-6 sm:py-12 lg:px-6">
                    <div class="mx-auto max-w-3xl divide-y-2 divide-gray-200">
                        <dl class="mt-6 space-y-6 divide-y divide-gray-200">
                            {{-- Question/Answer block --}}
                            <div x-data="{ open: false }" class="pt-6" x-cloak>
                                <dt class="text-lg">
                                    <button
                                        type="button"
                                        x-description="Expand/collapse question button"
                                        class="flex w-full items-start justify-between text-left text-gray-400"
                                        aria-controls="faq-0"
                                        @click="open = !open"
                                        aria-expanded="false"
                                        x-bind:aria-expanded="open.toString()"
                                    >
                                        <span class="font-medium text-gray-900">
                                            What is the purpose of Boycott Israeli Tech?
                                        </span>
                                        <span class="ml-6 flex h-7 items-center">
                                            <svg
                                                class="h-6 w-6 rotate-0 transform"
                                                x-description="Expand/collapse icon, toggle classes based on question open state. Heroicon name: outline/chevron-down"
                                                x-state:on="Open"
                                                x-state:off="Closed"
                                                :class="{ '-rotate-180': open, 'rotate-0': !(open) }"
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
                                                    d="M19 9l-7 7-7-7"
                                                ></path>
                                            </svg>
                                        </span>
                                    </button>
                                </dt>
                                <dd class="mt-2 pr-12" id="faq-0" x-show="open">
                                    <p class="text-base text-gray-500">
                                        The platform aims to raise awareness about the Israeli tech industry's
                                        connections to the Israeli government and military. By boycotting Israeli tech,
                                        it seeks to hold those responsible for human rights abuses accountable.
                                    </p>
                                </dd>
                            </div>
                            {{-- End of Question/Answer block --}}

                            {{-- Question/Answer block --}}
                            <div x-data="{ open: false }" class="pt-6" x-cloak>
                                <dt class="text-lg">
                                    <button
                                        type="button"
                                        x-description="Expand/collapse question button"
                                        class="flex w-full items-start justify-between text-left text-gray-400"
                                        aria-controls="faq-0"
                                        @click="open = !open"
                                        aria-expanded="false"
                                        x-bind:aria-expanded="open.toString()"
                                    >
                                        <span class="font-medium text-gray-900">
                                            How accurate is the information provided on the site?
                                        </span>
                                        <span class="ml-6 flex h-7 items-center">
                                            <svg
                                                class="h-6 w-6 rotate-0 transform"
                                                x-description="Expand/collapse icon, toggle classes based on question open state. Heroicon name: outline/chevron-down"
                                                x-state:on="Open"
                                                x-state:off="Closed"
                                                :class="{ '-rotate-180': open, 'rotate-0': !(open) }"
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
                                                    d="M19 9l-7 7-7-7"
                                                ></path>
                                            </svg>
                                        </span>
                                    </button>
                                </dt>
                                <dd class="mt-2 pr-12" id="faq-0" x-show="open">
                                    <p class="text-base text-gray-500">
                                        The data is sourced from public resources, and while efforts are made to ensure
                                        accuracy, there may be some incorrect information. Users are encouraged to
                                        contact the platform to suggest corrections or improvements. We provide proof
                                        resource for each company/investor/person we add to meke sure the information as
                                        accurate as possible.
                                    </p>
                                </dd>
                            </div>
                            {{-- End of Question/Answer block --}}

                            {{-- Question/Answer block --}}
                            <div x-data="{ open: false }" class="pt-6" x-cloak>
                                <dt class="text-lg">
                                    <button
                                        type="button"
                                        x-description="Expand/collapse question button"
                                        class="flex w-full items-start justify-between text-left text-gray-400"
                                        aria-controls="faq-0"
                                        @click="open = !open"
                                        aria-expanded="false"
                                        x-bind:aria-expanded="open.toString()"
                                    >
                                        <span class="font-medium text-gray-900">
                                            Can I contribute by suggesting companies or alternatives?
                                        </span>
                                        <span class="ml-6 flex h-7 items-center">
                                            <svg
                                                class="h-6 w-6 rotate-0 transform"
                                                x-description="Expand/collapse icon, toggle classes based on question open state. Heroicon name: outline/chevron-down"
                                                x-state:on="Open"
                                                x-state:off="Closed"
                                                :class="{ '-rotate-180': open, 'rotate-0': !(open) }"
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
                                                    d="M19 9l-7 7-7-7"
                                                ></path>
                                            </svg>
                                        </span>
                                    </button>
                                </dt>
                                <dd class="mt-2 pr-12" id="faq-0" x-show="open">
                                    <p class="text-base text-gray-500">
                                        Absolutely. The platform welcomes user contributions to expand and refine its
                                        database. There are options to suggest new companies to boycott or propose
                                        alternative solutions.
                                    </p>
                                </dd>
                            </div>
                            {{-- End of Question/Answer block --}}

                            {{-- Question/Answer block --}}
                            <div x-data="{ open: false }" class="pt-6" x-cloak>
                                <dt class="text-lg">
                                    <button
                                        type="button"
                                        x-description="Expand/collapse question button"
                                        class="flex w-full items-start justify-between text-left text-gray-400"
                                        aria-controls="faq-0"
                                        @click="open = !open"
                                        aria-expanded="false"
                                        x-bind:aria-expanded="open.toString()"
                                    >
                                        <span class="font-medium text-gray-900">
                                            Does the platform collaborate with other organizations?
                                        </span>
                                        <span class="ml-6 flex h-7 items-center">
                                            <svg
                                                class="h-6 w-6 rotate-0 transform"
                                                x-description="Expand/collapse icon, toggle classes based on question open state. Heroicon name: outline/chevron-down"
                                                x-state:on="Open"
                                                x-state:off="Closed"
                                                :class="{ '-rotate-180': open, 'rotate-0': !(open) }"
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
                                                    d="M19 9l-7 7-7-7"
                                                ></path>
                                            </svg>
                                        </span>
                                    </button>
                                </dt>
                                <dd class="mt-2 pr-12" id="faq-0" x-show="open">
                                    <p class="text-base text-gray-500">
                                        Yes, it lists similar sites and organizations focusing on boycotting other
                                        goods, supporting Palestine, and educating people about the Palestinian cause.
                                        However, it clarifies that it is not responsible for their content.
                                    </p>
                                </dd>
                            </div>
                            {{-- End of Question/Answer block --}}
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
