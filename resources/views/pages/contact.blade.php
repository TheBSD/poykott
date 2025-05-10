<x-app-layout>
    <div class="min-h-screen">
        <div class="container mx-auto px-4 py-16">
            <!-- Hero Section -->
            <div class="mx-auto mb-16 max-w-4xl text-center">
                <h1 class="mb-6 text-4xl font-bold text-gray-900 md:text-5xl">Contact us</h1>
            </div>

            <!-- Contact Form Section -->
            <div class="mx-auto max-w-2xl">
                <div class="rounded-2xl bg-white p-8 shadow-xl">
                    <h2 class="mb-8 text-3xl font-bold text-gray-900">Get in Touch</h2>

                    @if ($errors->any())
                        <div class="mb-6 rounded-lg bg-red-100 p-4 text-red-700">
                            There were some errors with your submission. Please fix them below and try again.
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST">
                        @csrf
                        <x-honeypot />
                        <div class="space-y-6">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Name
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    class="w-full rounded-lg border border-gray-200 px-4 py-3 transition duration-200 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                />
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Email
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    class="w-full rounded-lg border border-gray-200 px-4 py-3 transition duration-200 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                />
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Message
                                    <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    name="message"
                                    rows="5"
                                    class="w-full rounded-lg border border-gray-200 px-4 py-3 transition duration-200 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                >
{{ old('message') }}</textarea
                                >
                                @error('message')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button
                                type="submit"
                                class="w-full transform rounded-lg bg-blue-600 px-6 py-3 font-medium text-white transition duration-200 hover:scale-[1.02] hover:bg-blue-700"
                            >
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
