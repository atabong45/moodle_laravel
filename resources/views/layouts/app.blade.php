<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Tableau de bord') | {{ config('app.name', 'LearnPlatform') }}</title>

    <link rel="icon" href="/favicon.svg" type="image/svg+xml">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts et Styles avec Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js avec plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .font-sans { font-family: 'Inter', sans-serif; }

        /* Arrière-plan subtil et élégant */
        body {
            background-color: #f8fafc; /* bg-gray-50 */
            background-image:
                linear-gradient(to bottom, rgba(255,255,255,0.8), rgba(255,255,255,0.8)),
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23e2e8f0' fill-opacity='0.4'%3E%3Cpath d='M0 38.59l2.83-2.83 1.41 1.41L1.41 40H0v-1.41zM0 1.4l2.83 2.83 1.41-1.41L1.41 0H0v1.41zM38.59 40l-2.83-2.83 1.41-1.41L40 38.59V40h-1.41zM40 1.41l-2.83 2.83-1.41-1.41L38.59 0H40v1.41z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="h-full font-sans antialiased">
<div class="min-h-screen bg-transparent">
    {{-- Pour la version mobile, la navigation peut-être un composant Alpine.js qui se superpose --}}
    @include('layouts.navigation')

    <div class="flex flex-1 flex-col">
        <main class="flex-1">
            <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

                {{-- Fil d'Ariane --}}
                @php
                    $segments = array_filter(request()->segments());
                    $breadcrumbs = [['name' => 'Accueil', 'url' => url('/')]];
                    $path = '';
                    $translations = ['dashboard' => 'Tableau de bord', 'courses' => 'Cours', 'create' => 'Créer', 'edit' => 'Modifier', 'users' => 'Utilisateurs', 'profile' => 'Profil', 'assignments' => 'Évaluations', 'submissions' => 'Soumissions', 'about' => 'À Propos', 'contact' => 'Contact'];
                    foreach ($segments as $segment) {
                        if(is_numeric($segment)) continue;
                        $path .= '/' . $segment;
                        $title = $translations[$segment] ?? ucfirst(str_replace('-', ' ', $segment));
                        $breadcrumbs[] = ['name' => $title, 'url' => url($path)];
                    }
                @endphp

                @if(count($breadcrumbs) > 1)
                    <nav aria-label="Breadcrumb" class="mb-8">
                        <ol class="flex items-center space-x-1.5 text-sm">
                            @foreach ($breadcrumbs as $breadcrumb)
                                <li>
                                    <div class="flex items-center">
                                        @if (!$loop->first)
                                            <i class="fas fa-chevron-right text-gray-400 text-xs mx-1.5"></i>
                                        @endif
                                        <a href="{{ $breadcrumb['url'] }}" class="rounded-md px-2 py-1 @if(!$loop->last) text-gray-500 hover:text-indigo-600 hover:bg-gray-100 @else text-gray-800 font-semibold bg-gray-200/80 pointer-events-none @endif transition-colors">
                                            {{ $breadcrumb['name'] }}
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    </nav>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</div>

<!-- =================================================== -->
<!-- == SYSTÈME DE NOTIFICATION "TOAST" DE LUXE        == -->
<!-- =================================================== -->
<div
    x-data="notificationSystem()"
    @notify.window="add($event.detail)"
    class="fixed bottom-5 right-5 z-[100] w-full max-w-sm space-y-3"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="transform opacity-0 translate-y-full"
            x-transition:enter-end="transform opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-400"
            x-transition:leave-start="transform opacity-100 translate-y-0"
            x-transition:leave-end="transform opacity-0 -translate-y-full"
            class="relative w-full rounded-xl bg-white shadow-2xl overflow-hidden"
        >
            <div class="flex items-start p-4">
                <div class="shrink-0 text-2xl" :class="toast.colors.icon">
                    <i :class="toast.icon"></i>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p class="text-md font-semibold text-gray-900" x-text="toast.title"></p>
                    <p class="mt-1 text-md text-gray-600" x-text="toast.message"></p>
                </div>
                <div class="ml-4 flex shrink-0">
                    <button @click="remove(toast.id)" class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- Barre de progression -->
            <div class="absolute bottom-0 left-0 h-1" :class="toast.colors.progress" :style="`width: ${toast.percent}%; transition: width 0.05s linear;`"></div>
        </div>
    </template>
</div>


<script>
// ===============================================
// == LOGIQUE ALPINE.JS POUR LES NOTIFICATIONS  ==
// ===============================================
function notificationSystem() {
    return {
        toasts: [],
        counter: 0,
        config: {
            success: { title: 'Succès', icon: 'fas fa-check-circle', colors: { icon: 'text-green-500', progress: 'bg-green-500' } },
            error:   { title: 'Erreur',   icon: 'fas fa-times-circle',  colors: { icon: 'text-red-500',   progress: 'bg-red-500' } },
            alert:   { title: 'Alerte',   icon: 'fas fa-exclamation-triangle', colors: { icon: 'text-amber-500', progress: 'bg-amber-500' } },
        },

        add(detail) {
            const id = this.counter++;
            const toast = {
                id: id,
                type: detail.type || 'success',
                message: detail.message || 'Opération réussie.',
                ...this.config[detail.type || 'success'],
                show: true,
                percent: 100,
            };
            this.toasts.push(toast);
            this.startTimer(id);
        },

        remove(id) {
            const index = this.toasts.findIndex(t => t.id === id);
            if(index > -1) {
                this.toasts[index].show = false;
                // On nettoie le tableau après l'animation
                setTimeout(() => this.toasts.splice(index, 1), 500);
            }
        },

        startTimer(id) {
            const duration = 5000; // 5 secondes
            let remaining = duration;
            const interval = 50; // mise à jour toutes les 50ms

            const timer = setInterval(() => {
                const index = this.toasts.findIndex(t => t.id === id);
                if (index === -1) {
                    clearInterval(timer);
                    return;
                }

                remaining -= interval;
                this.toasts[index].percent = (remaining / duration) * 100;

                if (remaining <= 0) {
                    clearInterval(timer);
                    this.remove(id);
                }
            }, interval);
        }
    }
}
</script>

{{-- Déclencher les notifications de session Laravel via un événement global --}}
@if($message = session('success') ?? session('error') ?? session('alert'))
    @php
        $type = session('success') ? 'success' : (session('error') ? 'error' : 'alert');
    @endphp
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(new CustomEvent('notify', {
                detail: {
                    type: '{{ $type }}',
                    message: "{{ addslashes($message) }}"
                }
            }));
        });
    </script>
@endif

@stack('scripts')

</body>
</html>