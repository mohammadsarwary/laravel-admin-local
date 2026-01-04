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
        body {
            background: #1a1a1a;
            color: #e0e0e0;
        }
        .sidebar-bg {
            background: linear-gradient(135deg, #2d2d2d 0%, #1f1f1f 100%);
        }
        .card-dark {
            background: #2a2a2a;
            border: 1px solid #3a3a3a;
        }
        .accent-red {
            color: #ef4444;
        }
        .accent-blue {
            color: #3b82f6;
        }
    </style>
    
    @stack('styles')
</head>
<body class="min-h-screen" x-data="{ sidebarOpen: true }">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside 
            class="sidebar-bg text-white w-64 flex-shrink-0 transition-all duration-300 border-r border-gray-700"
            :class="{ 'w-64': sidebarOpen, 'w-20': !sidebarOpen }"
        >
            <div class="p-4 border-b border-gray-700 flex items-center space-x-2">
                <div class="w-8 h-8 bg-red-500 rounded flex items-center justify-center font-bold text-white">
                    M
                </div>
                <h1 class="text-lg font-bold" x-show="sidebarOpen">MarketAdmin</h1>
            </div>
            
            <nav class="mt-6 space-y-1">
                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider" x-show="sidebarOpen">
                    Main
                </div>
                
                <a href="{{ route('admin.web.dashboard') }}" 
                   class="flex items-center px-4 py-3 mx-2 rounded-lg transition-colors {{ request()->routeIs('admin.web.dashboard') ? 'bg-red-500 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                    <span class="material-icons text-xl">dashboard</span>
                    <span class="ml-3" x-show="sidebarOpen">Dashboard</span>
                </a>
                
                <a href="{{ route('admin.web.users') }}" 
                   class="flex items-center px-4 py-3 mx-2 rounded-lg transition-colors {{ request()->routeIs('admin.web.users') ? 'bg-red-500 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                    <span class="material-icons text-xl">people</span>
                    <span class="ml-3" x-show="sidebarOpen">Users</span>
                </a>
                
                <a href="{{ route('admin.web.ads') }}" 
                   class="flex items-center px-4 py-3 mx-2 rounded-lg transition-colors {{ request()->routeIs('admin.web.ads') ? 'bg-red-500 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                    <span class="material-icons text-xl">inventory_2</span>
                    <span class="ml-3" x-show="sidebarOpen">Listings</span>
                </a>
                
                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-6" x-show="sidebarOpen">
                    Management
                </div>
                
                <a href="{{ route('admin.web.reports') }}" 
                   class="flex items-center px-4 py-3 mx-2 rounded-lg transition-colors {{ request()->routeIs('admin.web.reports') ? 'bg-red-500 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                    <span class="material-icons text-xl">flag</span>
                    <span class="ml-3" x-show="sidebarOpen">Reports</span>
                </a>
                
                <a href="{{ route('admin.web.analytics') }}" 
                   class="flex items-center px-4 py-3 mx-2 rounded-lg transition-colors {{ request()->routeIs('admin.web.analytics') ? 'bg-red-500 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                    <span class="material-icons text-xl">analytics</span>
                    <span class="ml-3" x-show="sidebarOpen">Analytics</span>
                </a>
            </nav>

            <!-- User Profile at Bottom -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-700 sidebar-bg" :class="{ 'w-64': sidebarOpen, 'w-20': !sidebarOpen }">
                <div class="flex items-center space-x-3" x-show="sidebarOpen">
                    <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-orange-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-400 truncate">Super Admin</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="card-dark shadow-lg border-b border-gray-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-white transition-colors">
                            <span class="material-icons">menu</span>
                        </button>
                        <h2 class="text-2xl font-semibold text-white">@yield('header', 'Dashboard')</h2>
                    </div>
                    
                    <div class="flex items-center space-x-6">
                        <button class="text-gray-400 hover:text-white transition-colors">
                            <span class="material-icons">search</span>
                        </button>
                        <button class="text-gray-400 hover:text-white transition-colors relative">
                            <span class="material-icons">notifications</span>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-gray-300 hover:text-white transition-colors">
                                <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-orange-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                                </div>
                                <span class="text-sm">{{ auth()->user()->name ?? 'Admin' }}</span>
                                <span class="material-icons text-sm">arrow_drop_down</span>
                            </button>
                            
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-cloak
                                 class="absolute right-0 mt-2 w-48 card-dark rounded-lg shadow-xl py-1 z-50 border border-gray-700">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 transition-colors">Profile</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 transition-colors">Settings</a>
                                <hr class="my-1 border-gray-700">
                                <form method="POST" action="{{ route('auth.logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-500 hover:text-red-400 hover:bg-gray-700 transition-colors">
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
                    <div class="mb-4 bg-green-900 border border-green-700 text-green-300 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-900 border border-red-700 text-red-300 px-4 py-3 rounded-lg">
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
