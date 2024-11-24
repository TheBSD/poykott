<x-app-layout>
    <!-- Main Section -->
    <main class="container mx-auto space-y-6 p-6">
        <!-- Title -->
        <section class="mb-16 space-y-4 text-center">
            <h1 class="mt-12 text-6xl font-extrabold">Boycott Israeli Tech</h1>
            <p class="text-xl text-gray-400">
                Search for Israeli tech
                <strong>people</strong>
                .
            </p>
            <!-- Search -->
            <div class="relative mx-auto max-w-lg">
                <input
                    type="text"
                    name="search"
                    placeholder="Search person name or description..."
                    class="w-full rounded-md border border-blue-700 py-2 pl-10 pr-4 focus:outline-none focus:ring-1 focus:ring-blue-500"
                />
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="h-5 w-5 text-gray-400"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"
                        />
                    </svg>
                </div>
            </div>
        </section>

        <!-- Products Grid -->
        <section id="person-list" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <!-- Product Card -->
            @foreach ($people as $person)
                <div class="space-y-2 rounded-lg border border-blue-700 p-4">
                    <div class="flex items-center justify-between">
                        <img
                            src="{{ $person->getFirstMediaUrl() }}"
                            class="h-24 w-24 rounded-full object-cover"
                            alt="logo"
                            loading="lazy"
                        />
                        <h3 class="text-xl font-semibold">{{ $person->name }}</h3>
                    </div>
                    <p class="text-gray-400">{{ Str::limit($person->description, 100) }}</p>
                    <div class="flex items-center justify-between gap-2 text-sm">
                        <a href="{{ route('people.show', $person) }}" class="text-blue-400">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="h-6 w-6"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                />
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </section>
    </main>

    <!-- Show More Button -->
    <button id="show-more" class="mx-auto mb-12 mt-4 block rounded-md bg-blue-500 px-4 py-2 text-xl text-white">
        Show More People
    </button>

    <script>
        function createPeopleCard(person) {
            return `
                <div class="p-4 rounded-lg space-y-2 border border-blue-700">
                    <div class="flex justify-between items-center">
                        <img src="${person}" class="rounded-full h-[100px] object-cover" width="100" alt="logo" loading="lazy">
                        <h3 class="text-xl font-semibold">${person.name}</h3>
                    </div>
                    <p class="text-gray-400">${person.description?.substring(0, 100) ?? ''}</p>
                    <div class="flex gap-2 justify-between items-center text-sm">
                        <a href="/people/${person.id}"  class="text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </a>
                    </div>
                </div>
            `;
        }
    </script>

    <script>
        let page = 1;
        document.getElementById('show-more').addEventListener('click', async function () {
            try {
                page++;
                const response = await fetch(`people/load-more?page=${page}`);
                if (!response.ok) throw new Error('Network response was not ok');

                const data = await response.json();
                const personList = document.getElementById('person-list');

                // Hide show more button if no more pages
                if (page >= data.people.last_page) {
                    document.getElementById('show-more').style.display = 'none';
                }

                data.people.data.forEach((person) => {
                    const personCard = createPeopleCard(person);
                    personList.insertAdjacentHTML('beforeend', personCard);
                });
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to load more people. Please try again.');
            }
        });
    </script>

    <script>
        let searchTimeout;
        document.querySelector('input[name="search"]').addEventListener('input', async function () {
            try {
                const query = this.value;

                // Clear the previous timeout
                clearTimeout(searchTimeout);

                // Set a new timeout to delay the search
                searchTimeout = setTimeout(async () => {
                    // Show loading state
                    const personList = document.getElementById('person-list');
                    personList.innerHTML = '<div class="text-center">Loading...</div>';

                    const response = await fetch(`people/search?search=${encodeURIComponent(query)}`);
                    if (!response.ok) throw new Error('Network response was not ok');

                    const data = await response.json();
                    personList.innerHTML = ''; // Clear loading message

                    // Hide/show show more button based on search
                    document.getElementById('show-more').style.display = query ? 'none' : 'block';

                    if (data.people.data.length === 0) {
                        personList.innerHTML = '<div class="text-center text-gray-500">No people found</div>';
                        return;
                    }

                    data.people.data.forEach((person) => {
                        const personCard = createPeopleCard(person);
                        personList.insertAdjacentHTML('beforeend', personCard);
                    });
                }, 500); // Debounce for 500ms
            } catch (error) {
                console.error('Error:', error);
                const personList = document.getElementById('person-list');
                personList.innerHTML = '<div class="text-center text-red-500">Error loading people</div>';
            }
        });
    </script>
</x-app-layout>
