<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="container mx-auto px-4 py-16">
            <!-- Hero Section -->
            <div class="max-w-4xl mx-auto text-center mb-16">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">About Us</h1>
                <div class="prose prose-lg max-w-none">
                    <p class="text-gray-600 leading-relaxed">
                        Boycott Israeli Tech is a project that aims to raise awareness
                        about the Israeli tech industry and its connections to the Israeli
                        government and military. We believe that by boycotting Israeli tech,
                        we can help bring about change and hold those responsible for human rights
                        abuses accountable.
                    </p>
                </div>
            </div>

            <!-- Contact Form Section -->
            <div class="max-w-2xl mx-auto">
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-8">Get in Touch</h2>

                    <form action="{{ route('contact') }}" method="POST">
                        @csrf

                        <div class="space-y-6">
                            <div>
                                <label class="text-sm font-medium text-gray-700 block mb-2">Name</label>
                                <input type="text" name="name"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                    required>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-700 block mb-2">Email</label>
                                <input type="email" name="email"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                    required>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-700 block mb-2">Message</label>
                                <textarea name="message" rows="5"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                    required></textarea>
                            </div>

                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 transform hover:scale-[1.02]">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
