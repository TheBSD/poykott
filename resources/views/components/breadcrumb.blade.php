@props([
    'items',
])

<nav class="mx-8 mb-4 sm:mx-0">
    <ol class="flex items-center space-x-2 text-sm text-gray-500">
        @foreach ($items as $index => $item)
            <li class="flex items-center">
                @if ($index > 0)
                    <svg class="mx-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"
                        />
                    </svg>
                @endif

                @if ($item['url'] && $loop->last)
                    <span class="font-medium text-gray-900">{{ $item['name'] }}</span>
                @elseif ($item['url'])
                    <a href="{{ $item['url'] }}" class="hover:text-gray-700 hover:underline">{{ $item['name'] }}</a>
                @else
                    <span>{{ $item['name'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
