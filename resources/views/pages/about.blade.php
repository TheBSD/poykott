<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="container mx-auto px-4 py-16">
            <!-- Hero Section -->
            <div class="mx-auto mb-16 max-w-4xl text-center">
                <h1 class="mb-6 text-4xl font-bold text-gray-900 md:text-5xl">About Us</h1>
                <div class="prose prose-lg max-w-none">
                    <p class="leading-relaxed text-gray-600">
                        Boycott Israeli Tech is a project that aims to raise awareness about the Israeli tech industry
                        and its connections to the Israeli government and military. We believe that by boycotting
                        Israeli tech, we can help bring about change and hold those responsible for human rights abuses
                        accountable.
                    </p>
                </div>
            </div>

            <!-- Contact Form Section -->
            <div class="mx-auto max-w-2xl">
                <div class="rounded-2xl bg-white p-8 shadow-xl">
                    <h2 class="mb-8 text-3xl font-bold text-gray-900">Get in Touch</h2>

                    <form action="{{ route('contact') }}" method="POST">
                        @csrf
                        <x-honeypot />
                        <div class="space-y-6">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Name</label>
                                <input
                                    type="text"
                                    name="name"
                                    class="w-full rounded-lg border border-gray-200 px-4 py-3 transition duration-200 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                />
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Email</label>
                                <input
                                    type="email"
                                    name="email"
                                    class="w-full rounded-lg border border-gray-200 px-4 py-3 transition duration-200 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                />
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Message</label>
                                <textarea
                                    name="message"
                                    rows="5"
                                    class="w-full rounded-lg border border-gray-200 px-4 py-3 transition duration-200 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required
                                ></textarea>
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
