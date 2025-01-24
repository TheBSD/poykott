<x-app-layout>
    <!-- Main Section -->
    <main class="container mx-auto space-y-6 p-6">
        <!-- Title -->
        <section class="mb-16 space-y-4 text-center">
            <div class="flex justify-end">
                <a
                    href="{{ route('add-company') }}"
                    class="rounded-md bg-slate-900 p-3 text-lg text-white hover:bg-slate-600"
                >
                    New Company
                </a>
            </div>
            <h1 class="mt-12 text-6xl font-extrabold">Boycott Israeli Tech</h1>
            <p class="text-xl text-gray-400">
                Search for Israeli tech
                <strong>companies</strong>
                .
            </p>
            <!-- Search -->
            <div class="relative mx-auto max-w-lg">
                <input
                    type="text"
                    name="search"
                    placeholder="Search company name or description..."
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
        <section id="company-list" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <!-- Product Card -->
            @foreach ($companies as $company)
                <a
                    href="{{ route('companies.show', $company) }}"
                    class="block space-y-2 rounded-lg border border-blue-700 bg-white p-4 transition duration-300 ease-in-out hover:scale-105 hover:border-blue-500 hover:bg-gray-50 hover:shadow-lg"
                >
                    <div class="flex items-center justify-between">
                        <img src="{{ $company->image_path }}" width="100" alt="logo" loading="lazy" />
                        <h3 class="text-xl font-semibold">{{ $company->name }}</h3>
                    </div>
                    <p class="text-gray-400">{{ Str::limit($company->description, 100) }}</p>
                    <div class="flex items-center justify-end gap-2 text-sm">
                        <!-- Tags -->
                        <div class="flex flex-wrap gap-1">
                            @foreach ($company->tagsRelation as $tag)
                                <span class="rounded-md border border-blue-400 px-2 py-1 text-blue-400">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </a>
            @endforeach

            {{-- Maybe I will add simple pagination for SEO --}}
            {{-- {{$companies->links()}} --}}
        </section>
    </main>

    <!-- Show More Button -->
    <button id="show-more" class="mx-auto mb-12 mt-4 block rounded-md bg-blue-500 px-4 py-2 text-xl text-white">
        Show More Companies
    </button>

    <script>
        function createCompanyCard(company) {
            return `
                <a href="/companies/${company.slug}" class="block space-y-2 rounded-lg border border-blue-700 bg-white p-4 transition duration-300 ease-in-out hover:border-blue-500 hover:shadow-lg hover:scale-105 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <img src="${company.image_path}" width="100" alt="logo" loading="lazy">
                        <h3 class="text-xl font-semibold">${company.name}</h3>
                    </div>
                    <p class="text-gray-400">${company.description?.substring(0, 100) ?? ''}</p>
                    <div class="flex items-center justify-end gap-2 text-sm">
                        <div class="flex flex-wrap gap-1">
                            ${company.tags_relation.map((tag) => `<span class="rounded-md border border-blue-400 px-2 py-1 text-blue-400">${tag.name}</span>`).join('')}
                        </div>
                    </div>
                </a>
            `;
        }

        let page = 1;
        document.getElementById('show-more').addEventListener('click', async function () {
            try {
                page++;
                const response = await fetch(`/load-more?page=${page}`);
                if (!response.ok) throw new Error('Network response was not ok');

                const data = await response.json();
                const companyList = document.getElementById('company-list');

                console.log(data);

                // Hide show more button if no more pages
                if (page >= data.companies.last_page) {
                    document.getElementById('show-more').style.display = 'none';
                }

                data.companies.data.forEach((company) => {
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
        document.querySelector('input[name="search"]').addEventListener('input', async function () {
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

                    // Hide/show more button based on search
                    document.getElementById('show-more').style.display = query ? 'none' : 'block';

                    if (data.companies.data.length === 0) {
                        companyList.innerHTML = '<div class="text-center text-gray-500">No companies found</div>';
                        return;
                    }

                    data.companies.data.forEach((company) => {
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
</x-app-layout>
