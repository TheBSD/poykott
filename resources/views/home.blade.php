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
                <input type="text" name="search" placeholder="Search for a company..."
                    class="w-full border border-blue-700 pl-10 pr-4 py-2 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
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
        <section id="company-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Product Card -->
            @foreach ($companies as $company)
                <div class="p-4 rounded-lg space-y-2 border border-blue-700">
                    <h3 class="text-xl font-semibold">{{ $company->name }}</h3>
                    <p class="text-gray-400">{{ Str::limit($company->description, 100) }}</p>
                    <div class="flex gap-2 justify-between items-center text-sm">
                        <button class="text-blue-400" onclick="showModal({{ $company }})">
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
            @endforeach
        </section>

    </main>

    <!-- Show More Button -->
    <button id="show-more" class="mb-12 mt-4 text-xl px-4 py-2 bg-blue-500 text-white rounded-md mx-auto block">
        Show More Companies
    </button>

    <!-- Single Modal -->
    <div id="company-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center"
        onclick="if(event.target === this) this.classList.add('hidden')">
        <div class="bg-white p-6 rounded-lg space-y-4 w-[800px]" onclick="event.stopPropagation()">
            <h2 id="modal-title" class="text-2xl font-bold"></h2>
            <p id="modal-description"></p>
            <button class="text-red-400 border border-red-400 px-4 py-2 rounded-md"
                onclick="document.getElementById('company-modal').classList.add('hidden')">Close</button>
        </div>
    </div>


    <script>
        function showModal(company) {
            document.getElementById('modal-title').textContent = company.name;
            document.getElementById('modal-description').textContent = company.description;
            document.getElementById('company-modal').classList.remove('hidden');
        }
    </script>

    <script>
        function createCompanyCard(company) {
            return `
                <div class="p-4 rounded-lg space-y-2 border border-blue-700">
                    <h3 class="text-xl font-semibold">${company.name}</h3>
                    <p class="text-gray-400">${company.description?.substring(0, 100) ?? ''}</p>
                    <div class="flex gap-2 justify-between items-center text-sm">
                        <button class="text-blue-400" onclick='showModal(${JSON.stringify(company)})'>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                        <div class="flex flex-wrap gap-1">
                            ${company.tags_relation.map(tag => `<a href="#" class="text-blue-400 px-2 py-1 rounded-md border border-blue-400 hover:bg-blue-400 hover:text-white">${tag.name}</a>`).join('')}
                        </div>
                    </div>
                </div>
            `;
        }

        let page = 1;
        document.getElementById('show-more').addEventListener('click', async function() {
            try {
                page++;
                const response = await fetch(`/load-more?page=${page}`);
                if (!response.ok) throw new Error('Network response was not ok');

                const data = await response.json();
                const companyList = document.getElementById('company-list');

                // Hide show more button if no more pages
                if (page >= data.companies.last_page) {
                    document.getElementById('show-more').style.display = 'none';
                }

                data.companies.data.forEach(company => {
                    const companyCard = createCompanyCard(company);
                    companyList.insertAdjacentHTML('beforeend', companyCard);
                });
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to load more companies. Please try again.');
            }
        });
    </script>

    <script>
        let searchTimeout;
        document.querySelector('input[name="search"]').addEventListener('input', async function() {
            try {
                const query = this.value;

                // Clear the previous timeout
                clearTimeout(searchTimeout);

                // Set a new timeout to delay the search
                searchTimeout = setTimeout(async () => {
                    // Show loading state
                    const companyList = document.getElementById('company-list');
                    companyList.innerHTML = '<div class="text-center">Loading...</div>';

                    const response = await fetch(`/search?search=${encodeURIComponent(query)}`);
                    if (!response.ok) throw new Error('Network response was not ok');

                    const data = await response.json();
                    companyList.innerHTML = ''; // Clear loading message

                    // Hide/show show more button based on search
                    document.getElementById('show-more').style.display = query ? 'none' : 'block';

                    if (data.companies.data.length === 0) {
                        companyList.innerHTML =
                            '<div class="text-center text-gray-500">No companies found</div>';
                        return;
                    }

                    data.companies.data.forEach(company => {
                        const companyCard = createCompanyCard(company);
                        companyList.insertAdjacentHTML('beforeend', companyCard);
                    });
                }, 500); // Debounce for 500ms
            } catch (error) {
                console.error('Error:', error);
                const companyList = document.getElementById('company-list');
                companyList.innerHTML = '<div class="text-center text-red-500">Error loading companies</div>';
            }
        });
    </script>
</body>

</html>
