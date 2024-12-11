<header class="px-6 pt-10">
    <div class="flex items-center justify-between">
        <a href="{{ route('home') }}" class="text-4xl font-extrabold">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-[100px]" />
        </a>

        <!-- Mobile menu button -->
        <button class="lg:hidden" onclick="toggleMenu()">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Desktop navigation -->
        <nav class="hidden lg:block">
            <ul class="flex gap-6">
                <li>
                    <a
                        href="{{ route('home') }}"
                        class="{{ request()->is('/') ? 'text-blue-600' : '' }} text-lg font-bold uppercase hover:text-blue-600"
                    >
                        Companies
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('people') }}"
                        class="{{ request()->is('people') ? 'text-blue-600' : '' }} text-lg font-bold uppercase hover:text-blue-600"
                    >
                        People
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('investors') }}"
                        class="{{ request()->is('investors') ? 'text-blue-600' : '' }} text-lg font-bold uppercase hover:text-blue-600"
                    >
                        Investors
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('similar-sites') }}"
                        class="{{ request()->is('similar-sites') ? 'text-blue-600' : '' }} text-lg font-bold uppercase hover:text-blue-600"
                    >
                        Similar Sites
                    </a>
                </li>
            </ul>
        </nav>

        <a
            href="{{ route('about') }}"
            class="hidden rounded-md border border-yellow-600 px-4 py-2 text-yellow-600 lg:block"
        >
            About Us
        </a>
    </div>

    <!-- Mobile navigation -->
    <nav id="mobile-menu" class="mt-4 hidden lg:hidden">
        <ul class="flex flex-col gap-4">
            <li>
                <a
                    href="{{ route('home') }}"
                    class="{{ request()->is('/') ? 'text-blue-600' : '' }} text-lg font-bold uppercase hover:text-blue-600"
                >
                    Companies
                </a>
            </li>
            <li>
                <a
                    href="{{ route('people') }}"
                    class="{{ request()->is('people') ? 'text-blue-600' : '' }} text-lg font-bold uppercase hover:text-blue-600"
                >
                    People
                </a>
            </li>
            <li>
                <a
                    href="{{ route('investors') }}"
                    class="{{ request()->is('investors') ? 'text-blue-600' : '' }} text-lg font-bold uppercase hover:text-blue-600"
                >
                    Investors
                </a>
            </li>
            <li>
                <a
                    href="{{ route('similar-sites') }}"
                    class="{{ request()->is('similar-sites') ? 'text-blue-600' : '' }} text-lg font-bold uppercase hover:text-blue-600"
                >
                    Similar Sites
                </a>
            </li>
            <li>
                <a
                    href="{{ route('about') }}"
                    class="inline-block rounded-md border border-yellow-600 px-4 py-2 text-yellow-600"
                >
                    About Us
                </a>
            </li>
        </ul>
    </nav>
</header>

<script>
    function toggleMenu() {
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenu.classList.toggle('hidden');
    }
</script>
