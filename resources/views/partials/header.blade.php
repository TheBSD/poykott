<header class="pt-10 px-6 flex justify-between items-center">
    <a href="{{ route('home') }}" class="text-4xl font-extrabold">The BSD</a>
    <nav>
        <ul class="flex gap-6">
            <li><a href="{{ route('home') }}" class="hover:text-blue-600 font-bold text-lg uppercase {{ request()->is('/') ? 'text-blue-600' : '' }}">Companies</a></li>
            <li><a href="{{ route('people') }}" class="hover:text-blue-600 font-bold text-lg uppercase {{ request()->is('people') ? 'text-blue-600' : '' }}">People</a></li>
            <li><a href="{{ route('investors') }}" class="hover:text-blue-600 font-bold text-lg uppercase {{ request()->is('investors') ? 'text-blue-600' : '' }}">Investors</a></li>
        </ul>
    </nav>
    <button class="border border-yellow-600 text-yellow-600 px-4 py-2 rounded-md">Contact Us</button>
</header>