<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="container mx-auto px-4 py-16">
            <!-- Hero Section -->
            <div class="mx-auto mb-16 max-w-4xl text-center">
                <h1 class="mb-6 text-4xl font-bold text-gray-900 md:text-5xl">New Company</h1>
                <div class="prose prose-lg max-w-none">
                    <p class="leading-relaxed text-gray-600">Add new Israeli company to boycott</p>
                </div>
            </div>

            <!-- Contact Form Section -->
            <div class="mx-auto max-w-2xl">
                <div class="rounded-2xl bg-white p-8 shadow-xl">
                    <form action="{{ route('companies.store') }}" method="POST">
                        @csrf
                        <x-honeypot />
                        <div class="space-y-6">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Name
                                    <span class="inline text-lg text-red-600">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    class="w-full rounded-lg border border-gray-200 px-4 py-3 transition duration-200 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    value="{{ old('name') }}"
                                    required
                                />
                                <div class="text-red-500">
                                    {{ $errors->first('name') }}
                                </div>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    URL
                                    <span class="inline text-lg text-red-600">*</span>
                                </label>
                                <input
                                    type="url"
                                    name="url"
                                    class="w-full rounded-lg border border-gray-200 px-4 py-3 transition duration-200 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    value="{{ old('url') }}"
                                    required
                                />
                                <div class="text-red-500">
                                    {{ $errors->first('url') }}
                                </div>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Description</label>
                                <textarea
                                    name="description"
                                    rows="5"
                                    class="w-full rounded-lg border border-gray-200 px-4 py-3 transition duration-200 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
{{ old('description') }}</textarea
                                >
                                <div class="text-red-500">
                                    {{ $errors->first('description') }}
                                </div>
                            </div>

                            <button
                                type="submit"
                                class="w-full transform rounded-lg bg-blue-600 px-6 py-3 font-medium text-white transition duration-200 hover:scale-[1.02] hover:bg-blue-700"
                            >
                                Save Company
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
