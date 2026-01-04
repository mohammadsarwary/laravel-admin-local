<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name') }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen" x-data="{ sidebarOpen: true }">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside 
            class="bg-gray-800 text-white w-64 flex-shrink-0 transition-all duration-300"
            :class="{ 'w-64': sidebarOpen, 'w-20': !sidebarOpen }"
        >
            <div class="p-4 border-b border-gray-700">
                <h1 class="text-xl font-bold" x-show="sidebarOpen">Market Local</h1>
                <span class="text-xl font-bold" x-show="!sidebarOpen">ML</span>
            </div>
            
            <nav class="mt-4">
                <a href="{{ route('admin.web.dashboard') }}" 
                   class="flex items-center px-4 py-3 hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.web.dashboard') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}">
                    <span class="material-icons">dashboard</span>
                    <span class="ml-3" x-show="sidebarOpen">Dashboard</span>
                </a>
                
                <a href="{{ route('admin.web.users') }}" 
                   class="flex items-center px-4 py-3 hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.web.users') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}">
                    <span class="material-icons">people</span>
                    <span class="ml-3" x-show="sidebarOpen">Users</span>
                </a>
                
                <a href="{{ route('admin.web.ads') }}" 
                   class="flex items-center px-4 py-3 hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.web.ads') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}">
                    <span class="material-icons">inventory_2</span>
                    <span class="ml-3" x-show="sidebarOpen">Ads</span>
                </a>
                
                <a href="{{ route('admin.web.reports') }}" 
                   class="flex items-center px-4 py-3 hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.web.reports') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}">
                    <span class="material-icons">flag</span>
                    <span class="ml-3" x-show="sidebarOpen">Reports</span>
                </a>
                
                <a href="{{ route('admin.web.analytics') }}" 
                   class="flex items-center px-4 py-3 hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.web.analytics') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}">
                    <span class="material-icons">analytics</span>
                    <span class="ml-3" x-show="sidebarOpen">Analytics</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700">
                            <span class="material-icons">menu</span>
                        </button>
                        <h2 class="ml-4 text-xl font-semibold text-gray-800">@yield('header', 'Dashboard')</h2>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                                <span class="material-icons">account_circle</span>
                                <span>{{ auth()->user()->name ?? 'Admin' }}</span>
                                <span class="material-icons text-sm">arrow_drop_down</span>
                            </button>
                            
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-cloak
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('auth.logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
