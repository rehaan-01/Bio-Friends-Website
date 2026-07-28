<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100 text-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Biofriends ERP')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        kb: {
                            navy: '#1e3a8a',
                            blue: '#2563eb',
                            got: '#10b981',    // Money In / Stock In
                            gave: '#ef4444',   // Money Out / Consumed
                            card: '#ffffff',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- AG Grid JS & CSS -->
    <script src="https://cdn.jsdelivr.net/npm/ag-grid-community@32.3.3/dist/ag-grid-community.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@32.3.3/styles/ag-grid.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@32.3.3/styles/ag-theme-alpine.css">

    <style>
        .ag-theme-alpine {
            --ag-background-color: #ffffff;
            --ag-header-background-color: #f8fafc;
            --ag-odd-row-background-color: #ffffff;
            --ag-even-row-background-color: #f8fafc;
            --ag-row-hover-color: #f1f5f9;
            --ag-border-color: #e2e8f0;
            --ag-header-foreground-color: #475569;
            --ag-foreground-color: #0f172a;
            --ag-accent-color: #2563eb;
            --ag-border-radius: 12px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .kb-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
    </style>
</head>
<body class="h-full flex flex-col font-sans antialiased bg-slate-100 min-h-screen text-slate-900">

    <!-- Top Header Navigation -->
    <header class="sticky top-0 z-50 bg-slate-900 border-b border-slate-800 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- BioFriends Synergy Solutions Official Brand Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                    <div class="bg-white px-3 py-1.5 rounded-xl shadow-md border border-slate-700 flex items-center justify-center overflow-hidden hover:scale-105 transition duration-200">
                        <img src="{{ asset('images/logo.jpg') }}" alt="BioFriends Synergy Solutions Logo" class="h-9 w-auto object-contain">
                    </div>
                </a>

                <!-- Navigation Module Tabs & User Profile -->
                <div class="flex items-center space-x-4">
                    <nav class="flex items-center space-x-1.5">
                        <a href="{{ route('dashboard') }}" 
                           class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center space-x-1.5 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('inventory.index') }}" 
                           class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center space-x-1.5 {{ request()->routeIs('inventory.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span>Inventory</span>
                        </a>
                        <a href="{{ route('manufacturing.index') }}" 
                           class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center space-x-1.5 {{ request()->routeIs('manufacturing.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                            <span>Batches</span>
                        </a>
                        <a href="{{ route('billing.index') }}" 
                           class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center space-x-1.5 {{ request()->routeIs('billing.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            <span>Billing</span>
                        </a>
                    </nav>

                    @auth
                        <div class="flex items-center space-x-3 pl-3 border-l border-slate-800">
                            <div class="text-right hidden md:block">
                                <span class="text-xs font-bold text-white block">{{ Auth::user()->name }}</span>
                                <span class="text-[10px] text-emerald-400 font-mono">Administrator</span>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="bg-slate-800 hover:bg-rose-900/60 text-slate-300 hover:text-rose-300 text-xs font-bold p-2 rounded-xl border border-slate-700 transition flex items-center space-x-1" title="Sign Out">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    <span class="hidden sm:inline">Logout</span>
                                </button>
                            </form>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="w-7 h-7 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold">
                        ✓
                    </div>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white py-6 mt-12">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-2">
            <div>
                <span class="font-bold text-slate-800">Biofriends Synergy Solutions</span> &copy; {{ date('Y') }}. Business System.
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-emerald-600 flex items-center space-x-1 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                    <span>100% Secure Cloud System</span>
                </span>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
