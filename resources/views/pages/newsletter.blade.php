<x-app-layout>
    <div class="py-12">
        <h1 class="mb-6 text-center text-4xl font-bold md:text-5xl">Newsletter</h1>

        <div class="mx-auto max-w-2xl lg:text-center">
            <p class="mb-12 mt-6 text-lg text-gray-600">
                Sign up for our newsletter to get updates on our latest news and announcements.
            </p>
        </div>

        <div class="mx-auto max-w-7xl rounded-2xl bg-white p-8 px-6 py-12 shadow-xl sm:px-6 sm:py-12 lg:px-12">
            <!-- Newsletter -->
            <div class="mx-auto max-w-7xl py-12">
                <div class="mx-auto max-w-7xl lg:flex lg:items-center">
                    <div class="lg:w-0 lg:flex-1">
                        <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                            Sign up for our newsletter (Soon)
                        </h2>
                        <p class="mt-3 max-w-3xl text-lg text-gray-500">
                            Be up to date with our latest news and announcements.
                        </p>
                    </div>
                    <div class="mt-8 lg:ml-8 lg:mt-0">
                        <form class="sm:flex">
                            <label for="email-address" class="sr-only">Email address</label>
                            <input
                                id="email-address"
                                name="email-address"
                                type="email"
                                autocomplete="email"
                                required
                                class="w-full rounded-md border border-gray-300 px-5 py-3 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:max-w-xs"
                                placeholder="Enter your email"
                            />
                            <div class="mt-3 rounded-md shadow sm:ml-3 sm:mt-0 sm:flex-shrink-0">
                                <button
                                    type="submit"
                                    class="flex w-full items-center justify-center rounded-md border border-transparent bg-indigo-600 px-5 py-3 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                >
                                    Notify me
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
