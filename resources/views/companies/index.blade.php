<x-app-layout>
    <!-- Main Section -->
    <main class="container mx-auto min-h-screen space-y-6 p-6">
        <!-- Title -->
        <section class="text-center">
            <!-- Main Heading -->
            <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl md:text-6xl">
                <span class="block">Israeli Companies</span>
                <span class="mt-2 block text-xl font-semibold text-gray-500 sm:text-2xl">Boycott These Companies</span>
            </h1>

            <!-- Decorative elements (optional) -->
            <div class="mt-8 flex justify-center space-x-4">
                <div class="h-1.5 w-12 rounded-full bg-red-600"></div>
                <div class="h-1.5 w-6 rounded-full bg-red-400"></div>
                <div class="h-1.5 w-3 rounded-full bg-red-300"></div>
            </div>
        </section>

        <livewire:company-list />
    </main>
</x-app-layout>
