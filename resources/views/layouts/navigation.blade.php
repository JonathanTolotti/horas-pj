<nav x-data="{ open: false }" class="bg-gray-900 border-b border-gray-800">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-white font-semibold text-lg">Horas PJ</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('dashboard') ? 'border-cyan-400 text-white' : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-600' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                        Dashboard
                    </a>
                    <a href="{{ route('settings') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('settings') ? 'border-cyan-400 text-white' : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-600' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                        Configuracoes
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown + Privacy Toggle -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <!-- Privacy Toggle -->
                <button onclick="togglePrivacy()" id="privacy-toggle" title="Ocultar valores"
                    class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800 transition-colors">
                    <svg id="eye-open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg id="eye-closed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
                <!-- Subscription Badge -->
                @php
                    $badge = Auth::user()->getSubscriptionBadge();
                    $badgeColors = [
                        'gray' => 'bg-gray-600 text-gray-200',
                        'yellow' => 'bg-yellow-600 text-yellow-100',
                        'orange' => 'bg-orange-600 text-orange-100',
                        'green' => 'bg-green-600 text-green-100',
                    ];
                @endphp
                <a href="{{ route('subscription.plans') }}"
                   class="px-2 py-1 text-xs font-medium rounded {{ $badgeColors[$badge['color']] }} hover:opacity-80 transition-opacity">
                    {{ $badge['label'] }}
                </a>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-gray-700 text-sm leading-4 font-medium rounded-lg text-gray-300 bg-gray-800 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-cyan-500 transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="text-gray-300 hover:bg-gray-700">
                            {{ __('Perfil') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('subscription.manage')" class="text-gray-300 hover:bg-gray-700">
                            {{ __('Assinatura') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    class="text-gray-300 hover:bg-gray-700"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Sair') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden gap-1">
                <!-- Privacy Toggle Mobile -->
                <button onclick="togglePrivacy()" id="privacy-toggle-mobile" title="Ocultar valores"
                    class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800 transition-colors">
                    <svg id="eye-open-mobile" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg id="eye-closed-mobile" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-300 hover:bg-gray-800 focus:outline-none focus:bg-gray-800 focus:text-gray-300 transition duration-150 ease-in-out">
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
            <a href="{{ route('dashboard') }}"
               class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('dashboard') ? 'border-cyan-400 text-cyan-400 bg-gray-800' : 'border-transparent text-gray-400 hover:text-gray-300 hover:bg-gray-800 hover:border-gray-600' }} text-start text-base font-medium transition duration-150 ease-in-out">
                Dashboard
            </a>
            <a href="{{ route('settings') }}"
               class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('settings') ? 'border-cyan-400 text-cyan-400 bg-gray-800' : 'border-transparent text-gray-400 hover:text-gray-300 hover:bg-gray-800 hover:border-gray-600' }} text-start text-base font-medium transition duration-150 ease-in-out">
                Configuracoes
            </a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-700">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}"
                   class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-gray-400 hover:text-gray-300 hover:bg-gray-800 hover:border-gray-600 text-start text-base font-medium transition duration-150 ease-in-out">
                    Perfil
                </a>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit"
                            class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-gray-400 hover:text-gray-300 hover:bg-gray-800 hover:border-gray-600 text-start text-base font-medium transition duration-150 ease-in-out">
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
