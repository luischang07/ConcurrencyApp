<!doctype html>
<html lang="es" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'ConcurrencyApp' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('head')
</head>

<body class="bg-gray-50 min-h-screen font-sans flex flex-col dark:bg-gray-900 dark:text-gray-100">
    @include('components.header')

    <main class="flex-1 py-8 px-4">
        <div class="max-w-4xl mx-auto">
            @yield('content')
        </div>
    </main>

    @include('components.footer')

    @stack('scripts')
</body>

</html>
