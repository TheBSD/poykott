<header class="pt-10 px-6">
    <div class="flex justify-between items-center">
        <a href="{{ route('home') }}" class="text-4xl font-extrabold">The BSD</a>

        <!-- Mobile menu button -->
        <button class="lg:hidden" onclick="toggleMenu()">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Desktop navigation -->
        <nav class="hidden lg:block">
            <ul class="flex gap-6">
                <li><a href="{{ route('home') }}"
                        class="hover:text-blue-600 font-bold text-lg uppercase {{ request()->is('/') ? 'text-blue-600' : '' }}">Companies</a>
                </li>
                <li><a href="{{ route('people') }}"
                        class="hover:text-blue-600 font-bold text-lg uppercase {{ request()->is('people') ? 'text-blue-600' : '' }}">People</a>
                </li>
                <li><a href="{{ route('investors') }}"
                        class="hover:text-blue-600 font-bold text-lg uppercase {{ request()->is('investors') ? 'text-blue-600' : '' }}">Investors</a>
                </li>
            </ul>
        </nav>

        <a href="{{ route('about') }}"
            class="hidden lg:block border border-yellow-600 text-yellow-600 px-4 py-2 rounded-md">About Us</a>
    </div>

    <!-- Mobile navigation -->
    <nav id="mobile-menu" class="hidden lg:hidden mt-4">
        <ul class="flex flex-col gap-4">
            <li><a href="{{ route('home') }}"
                    class="hover:text-blue-600 font-bold text-lg uppercase {{ request()->is('/') ? 'text-blue-600' : '' }}">Companies</a>
            </li>
            <li><a href="{{ route('people') }}"
                    class="hover:text-blue-600 font-bold text-lg uppercase {{ request()->is('people') ? 'text-blue-600' : '' }}">People</a>
            </li>
            <li><a href="{{ route('investors') }}"
                    class="hover:text-blue-600 font-bold text-lg uppercase {{ request()->is('investors') ? 'text-blue-600' : '' }}">Investors</a>
            </li>
            <li><a href="{{ route('about') }}"
                    class="inline-block border border-yellow-600 text-yellow-600 px-4 py-2 rounded-md">About Us</a></li>
        </ul>
    </nav>
</header>

<script>
    function toggleMenu() {
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenu.classList.toggle('hidden');
    }
</script>
