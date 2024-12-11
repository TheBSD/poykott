<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="container mx-auto px-4 py-16">
            <!-- Hero Section -->
            <div class="mx-auto mb-16 max-w-4xl text-center">
                <h1 class="mb-6 text-4xl font-bold text-gray-900 md:text-5xl">Similar Sites</h1>
                <div class="prose prose-lg max-w-none">
                    <p class="leading-relaxed text-gray-600">
                        Here we are focusing on boycotting israeli tech. There are many friends sites who are focusing
                        one boycotting other goods the supports israel, teaching people about the palestinian case, and
                        support palestine. Although we have shared goal we are not responsible for their content.
                    </p>
                </div>
            </div>

            <!-- Contact Form Section -->
            <div class="mx-auto max-w-2xl">
                <div class="rounded-2xl bg-white p-8 shadow-xl">
                    <h2 class="mb-8 text-3xl font-bold text-gray-900">Here is the list</h2>
                    <ul>
                        <div class="grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach ($sites as $parent)
                                @if ($parent->parent_id === null)
                                    <h3 class="text-lg font-bold">#{{ $parent->name }}</h3>
                                    <p class="text-gray-600">{{ $parent->description }}</p>
                                    @if ($parent->children()->exists())
                                        <ul>
                                            @foreach ($parent->children as $site)
                                                <li>
                                                    <a
                                                        href="{{ $site->url }}"
                                                        class="break-all text-blue-500 hover:underline"
                                                    >
                                                        {{ $site->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
