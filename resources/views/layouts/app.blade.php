<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'moodle-client') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">

    <!-- Styles -->
    @vite('resources/css/app.css')

    <!-- Scripts -->
    @vite('resources/js/app.js')

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen bg-gray-100">
        <!-- Barre de navigation -->
        @include('layouts.navigation')

        @if(session('error'))
            <div class="bg-red-500 text-white p-3 rounded-md alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-500 text-white p-3 rounded-md alert alert-success">
                {{ session('success') }}
            </div>
        @endif


        <!-- En-tête -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif


        


        <!-- Contenu principal -->
        <main class="py-8">

            <!-- Beadcrumb -->
             <div class = "container w-4/5  mx-auto flex flex-col justify-center">
               
                @php
                    $segments = request()->segments();
                    $breadcrumbs = [['name' => 'Accueil', 'url' => url('/')]];
                    $path = '';

                    foreach ($segments as $segment) {
                        $path .= '/' . $segment;
                        $breadcrumbs[] = ['name' => ucfirst(str_replace('-', ' ', $segment)), 'url' => url($path)];
                    }
                @endphp

                @if(count($breadcrumbs) > 1)
                    <nav aria-label="breadcrumb" class="bg-gray-100 p-3 rounded mb-4">
                        <ol class="breadcrumb flex space-x-2">
                            @foreach ($breadcrumbs as $breadcrumb)
                                @if (!$loop->last)
                                    <li>
                                        <a href="{{ $breadcrumb['url'] }}" class="text-blue-500 hover:underline">{{ $breadcrumb['name'] }}</a> >
                                    </li>
                                @else
                                    <li class="text-gray-700">{{ $breadcrumb['name'] }}</li>
                                @endif
                            @endforeach
                        </ol>
                    </nav>
                @endif
            </div>

            @yield('content')

            @yield('script')
        </main>
    </div>
</body>
<script>
    setTimeout(() => {
        document.querySelectorAll('.alert-danger').forEach(el => el.style.display = 'none');
    }, 5000); // Disparaît après 5 secondes
</script>
</html>
