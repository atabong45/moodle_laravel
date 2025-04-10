<nav x-data="{ open: false, isSyncing: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                     <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('Accueil') }}
                    </x-nav-link>
                    <x-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.index')">
                        {{ __('Cours') }}
                    </x-nav-link>
                    <x-nav-link :href="route('assignments.index')" :active="request()->routeIs('assignments.index')">
                        {{ __('Évaluations') }}
                    </x-nav-link>
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Tableau de bord') }}
                    </x-nav-link>

                    <!-- Admin Navigation -->
                    @role('ROLE_ADMIN')
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('Gestion des Utilisateurs') }}
                        </x-nav-link>
                    </div>
                    @endrole
                </div>
            </div>

            <form id="syncForm" action="{{ route('synchronisation') }}" method="POST">
                @csrf
                <div class="inline-flex items-center ml-[300px] py-5 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white">
                    <button type="submit" @click="isSyncing = true; setTimeout(() => isSyncing = false, 2000)" :class="{'animate-spin': isSyncing}" class=
                        " hover:text-gray-700 focus:outline-none transition ease-in-out duration-150 me-2">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <span>Actualiser </span>
                </div>
            </form>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                        <div>{{ Auth::user() ? Auth::user()->name : 'No User connected' }}</div>
                        <div class="ms-1">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <!-- Vérification si l'utilisateur est connecté -->
                    @auth
                        <!-- Si l'utilisateur est connecté -->
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profil') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                    this.closest('form').submit();">
                                {{ __('Deconnexion') }}
                            </x-dropdown-link>
                        </form>
                    @else
                        <!-- Si l'utilisateur n'est pas connecté -->
                        <x-dropdown-link :href="route('login')">
                            {{ __('Se connecter') }}
                        </x-dropdown-link>

                        <!-- <x-dropdown-link :href="route('register')">
                            {{ __('S\'inscrire') }}
                        </x-dropdown-link> -->
                    @endauth
                </x-slot>
            </x-dropdown>
            </div>

            <!-- About -->
            <div class="flex items-center text-gray-400 hover:text-gray-500">
                <a href="/about">
                    <i class="fas fa-info-circle"></i>
                </a>
            </div>

            <!-- Contact -->
            <div class="flex items-center text-gray-400 hover:text-gray-500">
                <a href="/contact">
                    <i class="fas fa-envelope"></i>
                </a>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ __('Accueil') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.index')">
                {{ __('Cours') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('assignments.index')" :active="request()->routeIs('assignments.index')">
                {{ __('Évaluations') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <!-- Admin Navigation -->
            @role('ROLE_ADMIN')
            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    {{ __('Gestion des Utilisateurs') }}
                </x-responsive-nav-link>
            </div>
            @endrole
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name ?? 'No User connected' }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email ?? 'No User connected' }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profil') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Déconnexion') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
