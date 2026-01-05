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
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
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
<body class="bg-gray-900 text-gray-100 antialiased" x-data="{ sidebarOpen: true, darkMode: true }" x-init="initDarkMode()">
    <script>
    function initDarkMode() {
        this.darkMode = localStorage.getItem('darkMode') !== 'false';
        this.applyDarkMode();
    }
    
    function toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
        this.applyDarkMode();
    }
    
    function applyDarkMode() {
        if (this.darkMode) {
            document.body.classList.remove('bg-gray-100', 'text-gray-900');
            document.body.classList.add('bg-gray-900', 'text-gray-100');
        } else {
            document.body.classList.remove('bg-gray-900', 'text-gray-100');
            document.body.classList.add('bg-gray-100', 'text-gray-900');
        }
    }
    </script>
    
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
                
                <a href="{{ route('admin.web.advertisements') }}" 
                   class="flex items-center px-4 py-3 mx-2 rounded-lg transition-colors {{ request()->routeIs('admin.web.advertisements') ? 'bg-red-500 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
                    <span class="material-icons text-xl">campaign</span>
                    <span class="ml-3" x-show="sidebarOpen">Advertisements</span>
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
                    
                    <div class="flex items-center space-x-4">
                        <button @click="toggleDarkMode()" class="p-2 rounded-lg hover:bg-gray-700 text-gray-400 hover:text-white transition-colors">
                            <span class="material-icons" x-text="darkMode ? 'light_mode' : 'dark_mode'"></span>
                        </button>
                        
                        <button @click="showHelpModal = true" class="p-2 rounded-lg hover:bg-gray-700 text-gray-400 hover:text-white transition-colors">
                            <span class="material-icons">help_outline</span>
                        </button>
                        
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

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

    <!-- Help Modal -->
    <div x-show="showHelpModal" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="showHelpModal = false" x-data="{ showHelpModal: false }">
        <div class="bg-gray-900 rounded-lg border border-gray-700 max-w-2xl w-full mx-4 shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white">Help & Documentation</h3>
                <button @click="showHelpModal = false" class="text-gray-400 hover:text-white transition-colors">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <div class="p-6">
                <div class="space-y-6">
                    <div>
                        <h4 class="text-white font-semibold mb-2">Keyboard Shortcuts</h4>
                        <ul class="text-gray-400 text-sm space-y-1">
                            <li><kbd class="bg-gray-800 px-2 py-1 rounded">Escape</kbd> - Close modals</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-white font-semibold mb-2">Navigation</h4>
                        <ul class="text-gray-400 text-sm space-y-1">
                            <li>Use the sidebar to navigate between pages</li>
                            <li>Click on column headers to sort tables</li>
                            <li>Use filters to narrow down results</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-white font-semibold mb-2">Actions</h4>
                        <ul class="text-gray-400 text-sm space-y-1">
                            <li>Click "View" to see detailed information</li>
                            <li>Click "Edit" to modify records</li>
                            <li>Use "Export" to download data in various formats</li>
                            <li>Use "Refresh" to reload data</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-white font-semibold mb-2">Need More Help?</h4>
                        <p class="text-gray-400 text-sm">Contact the development team or visit the documentation portal for detailed guides.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Global date formatting function for consistent date display across all admin pages
    function formatDate(dateString, includeTime = false) {
        if (!dateString) return 'N/A';
        
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return 'Invalid Date';
        
        if (includeTime) {
            return date.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } else {
            return date.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric',
                year: 'numeric'
            });
        }
    }
    
    // Token validation function
    function getAuthToken() {
        const token = localStorage.getItem('admin_token');
        if (!token) {
            // Redirect to login if no token found
            window.location.href = '/admin/login';
            return null;
        }
        return token;
    }
    
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        
        const colors = {
            success: 'bg-green-500/20 border-green-500/50 text-green-400',
            error: 'bg-red-500/20 border-red-500/50 text-red-400',
            warning: 'bg-yellow-500/20 border-yellow-500/50 text-yellow-400',
            info: 'bg-blue-500/20 border-blue-500/50 text-blue-400'
        };
        
        const icons = {
            success: 'check_circle',
            error: 'error',
            warning: 'warning',
            info: 'info'
        };
        
        toast.className = `flex items-center p-4 rounded-lg border ${colors[type]} shadow-lg transform transition-all duration-300 translate-x-full`;
        toast.innerHTML = `
            <span class="material-icons mr-3">${icons[type]}</span>
            <span>${message}</span>
            <button onclick="this.parentElement.remove()" class="ml-4 hover:opacity-70">
                <span class="material-icons text-sm">close</span>
            </button>
        `;
        
        container.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 10);
        
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    </script>

    <style>
    @media print {
        .sidebar,
        .fixed.top-0.left-0,
        .fixed.bottom-4.right-4,
        button,
        input[type="text"],
        input[type="date"],
        select,
        .pagination,
        .filters,
        .actions {
            display: none !important;
        }
        
        main {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        
        .card-dark,
        .bg-white.rounded-lg.shadow {
            box-shadow: none !important;
            border: 1px solid #ccc !important;
        }
        
        table {
            font-size: 10pt !important;
        }
        
        body {
            background: white !important;
            color: black !important;
        }
    }
    </style>

    @stack('scripts')
</body>
</html>
