<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>The BSD</title>

    @vite('resources/css/app.css')

</head>

<body class="">

    <!-- Header -->
    <header class="py-4 px-6 flex justify-between items-center">
        <div class="text-4xl font-extrabold">The BSD</div>
        <button class="border border-yellow-600 text-yellow-600
         px-4 py-2 rounded-md">Contact Us</button>
    </header>

    <!-- Main Section -->
    <main class="p-6 space-y-6 container mx-auto">
        <!-- Title -->
        <section class="text-center space-y-4 mb-16">
            <h1 class="text-4xl font-extrabold">
                Boycott Israeli Tech
            </h1>
            <p class="text-gray-400">
                Search for people and companies that support Israeli tech.
            </p>
            <!-- Search -->
            <div class="relative max-w-lg mx-auto">
                <input type="text" placeholder=""
                    class="w-full border border-blue-700
                     pl-10 pr-4 py-2 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
            </div>
        </section>


        <!-- Products Grid -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Product Card -->
            @foreach ($companies as $company)
                <div class="p-4 rounded-lg space-y-2 border border-blue-700">
                    <h3 class="text-xl font-semibold">{{ $company->name }}</h3>
                    <p class="text-gray-400">{{ Str::limit($company->description, 100) }}</p>
                    <div class="flex gap-2 justify-between items-center text-sm">
                        <button class="text-blue-400"
                            onclick="document.getElementById('modal-{{ $company->id }}').classList.remove('hidden')">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                        <!-- Tags -->
                        <div class="flex flex-wrap gap-1">
                            @foreach ($company->tagsRelation as $tag)
                                <a href="#"
                                    class="text-blue-400 px-2 py-1 rounded-md border border-blue-400 hover:bg-blue-400 hover:text-white">{{ $tag->name }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div id="modal-{{ $company->id }}"
                    class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center"
                    onclick="if(event.target === this) this.classList.add('hidden')">
                    <div class="bg-white p-6 rounded-lg space-y-4" onclick="event.stopPropagation()">
                        <h2 class="text-2xl font-bold">{{ $company->name }}</h2>
                        <p>{{ $company->description }}</p>
                        <button class="text-red-400"
                            onclick="document.getElementById('modal-{{ $company->id }}').classList.add('hidden')">Close</button>
                    </div>
                </div>
            @endforeach
        </section>

    </main>
</body>

</html>
