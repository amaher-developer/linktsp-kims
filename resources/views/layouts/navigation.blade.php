<nav x-data="{ open: false }" class="bg-white/80 backdrop-blur border-b border-gray-200 sticky top-0 z-30">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-white font-bold">K</span>
                        <span class="hidden sm:inline font-semibold text-gray-800">KIMS</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 rtl:space-x-reverse sm:-my-px sm:ms-10 sm:flex overflow-x-auto">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('admin.dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.branches.index')" :active="request()->routeIs('admin.branches.*')">
                        {{ __('admin.branches') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">
                        {{ __('admin.categories') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">
                        {{ __('admin.products') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.option-groups.index')" :active="request()->routeIs('admin.option-groups.*')">
                        {{ __('admin.options') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">
                        {{ __('admin.orders') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.loyalty-rules.index')" :active="request()->routeIs('admin.loyalty-rules.*')">
                        {{ __('admin.loyalty') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.rewards.index')" :active="request()->routeIs('admin.rewards.*')">
                        {{ __('admin.rewards') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.staff.index')" :active="request()->routeIs('admin.staff.*')">
                        {{ __('admin.staff') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')">
                        {{ __('admin.roles') }}
                    </x-nav-link>
                    <x-nav-link :href="route('admin.integrations.edit')" :active="request()->routeIs('admin.integrations.*')">
                        {{ __('admin.integrations') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <form method="POST" action="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-gray-200 text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                        {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
                    </button>
                </form>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('admin.profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('admin.log_out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
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
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('admin.dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.branches.index')" :active="request()->routeIs('admin.branches.*')">
                {{ __('admin.branches') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">
                {{ __('admin.categories') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">
                {{ __('admin.products') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.option-groups.index')" :active="request()->routeIs('admin.option-groups.*')">
                {{ __('admin.options') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">
                {{ __('admin.orders') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.loyalty-rules.index')" :active="request()->routeIs('admin.loyalty-rules.*')">
                {{ __('admin.loyalty') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.rewards.index')" :active="request()->routeIs('admin.rewards.*')">
                {{ __('admin.rewards') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.staff.index')" :active="request()->routeIs('admin.staff.*')">
                {{ __('admin.staff') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')">
                {{ __('admin.roles') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.integrations.edit')" :active="request()->routeIs('admin.integrations.*')">
                {{ __('admin.integrations') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('admin.profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}">
                    @csrf
                    <button type="submit" class="block w-full ps-3 pe-4 py-2 text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50">
                        {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
                    </button>
                </form>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('admin.log_out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
