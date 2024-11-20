<header class="flex items-center justify-between px-6 pt-10">
    <a href="{{ route('home') }}" class="text-4xl font-extrabold">The BSD</a>
    <nav>
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
        </ul>
    </nav>
    <button class="rounded-md border border-yellow-600 px-4 py-2 text-yellow-600">Contact Us</button>
</header>
