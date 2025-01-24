<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>Boycott Israeli Tech</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @include('partials.analytics')
    </head>

    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('partials.header')

            <!-- Success Message -->
            @if (session('success'))
                <div
                    id="success-alert"
                    class="fixed right-40 top-20 mx-auto flex w-fit items-center rounded-md border border-green-400 bg-green-100 px-4 py-3 text-green-700"
                    role="alert"
                >
                    <div>
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                    <button onclick="closeAlert()" class="ml-4 text-green-700 hover:text-green-900">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <script>
            // Auto-dismiss after 2 seconds
            setTimeout(() => {
                document.getElementById('success-alert')?.remove();
            }, 4000);

            // Close button handler
            function closeAlert() {
                document.getElementById('success-alert')?.remove();
            }
        </script>
    </body>
</html>
