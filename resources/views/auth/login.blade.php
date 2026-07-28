<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - BioFriends Synergy Solutions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-heading { font-family: 'Space Grotesk', sans-serif; }
        .kb-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
    </style>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 flex flex-col justify-between antialiased">

    <!-- Top ERP Branding Header -->
    <header class="bg-slate-900 border-b border-slate-800 shadow-md py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-white px-3 py-1.5 rounded-xl shadow-md border border-slate-700 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('images/logo.jpg') }}" alt="BioFriends Synergy Solutions Logo" class="h-9 w-auto object-contain">
                </div>
            </div>
            <span class="bg-emerald-500/20 text-emerald-400 text-xs font-bold uppercase px-3 py-1 rounded-full border border-emerald-500/30">Secure Portal</span>
        </div>
    </header>

    <!-- Main Content Centered Card -->
    <main class="flex-grow flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-md kb-card rounded-2xl p-6 sm:p-8 space-y-6">
            
            <!-- Card Header Banner with Agri-Bio Visual -->
            <div class="relative h-28 rounded-xl overflow-hidden bg-slate-900 flex items-end p-4 border border-slate-200 shadow-sm">
                <img src="{{ asset('images/hero-agri-golden.jpg') }}" alt="Agricultural Products" class="absolute inset-0 w-full h-full object-cover opacity-60">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/50 to-transparent"></div>
                <div class="relative z-10 text-white">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-400 block">Modern Farming ERP</span>
                    <h2 class="text-base font-heading font-extrabold leading-tight">BioFriends Synergy Portal</h2>
                </div>
            </div>

            <!-- Card Title -->
            <div class="text-center space-y-1 pt-1">
                <h1 class="text-2xl font-heading font-extrabold text-slate-900">Sign In to Your Account</h1>
                <p class="text-xs text-slate-500 font-medium">Enter your credentials to access the ERP business portal</p>
            </div>

            <!-- Error Message for Wrong Credentials -->
            @if ($errors->any())
                <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-start space-x-2">
                    <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-extrabold">Authentication Failed</p>
                        <p class="font-medium text-rose-600 mt-0.5">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            <!-- Session Status Notification -->
            @if (session('status'))
                <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs font-bold">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Login Form -->
            <form id="loginForm" method="POST" action="{{ route('login') }}" class="space-y-4" onsubmit="showLoadingState()" autocomplete="off">
                @csrf
                
                <!-- Anti-autofill dummy fields -->
                <input type="email" style="opacity: 0; position: absolute; z-index: -1;" name="fake_email_prevent_autofill">
                <input type="password" style="opacity: 0; position: absolute; z-index: -1;" name="fake_password_prevent_autofill">


                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase text-slate-600 mb-1">Email Address</label>
                    <div class="relative">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="new-password" required autofocus
                               placeholder="Enter your email address"
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-9 pr-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:border-blue-600 focus:bg-white transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                    </div>
                </div>

                <!-- Password Field with Show/Hide Toggle -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-xs font-bold uppercase text-slate-600">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:text-blue-800 font-bold">Forgot password?</a>
                        @endif
                    </div>
                    <div class="relative">
                        <input type="password" id="password" name="password" autocomplete="new-password" required
                               placeholder="••••••••"
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-9 pr-10 py-2.5 text-sm text-slate-900 focus:outline-none focus:border-blue-600 focus:bg-white transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <!-- Show / Hide Password Button -->
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs font-bold px-1 py-0.5 rounded focus:outline-none" title="Toggle password visibility">
                            <span id="eyeText">Show</span>
                        </button>
                    </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center justify-between text-xs text-slate-600 pt-1">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="remember" id="remember" checked class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="font-semibold text-slate-700">Remember me on this device</span>
                    </label>
                </div>

                <!-- Submit Button with Loading State -->
                <button type="submit" id="submitBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl text-sm transition shadow-md flex items-center justify-center space-x-2">
                    <span id="btnText">Sign In to Dashboard</span>
                    <svg id="btnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>

        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white py-4 text-center text-xs text-slate-500">
        <span class="font-bold text-slate-700">BioFriends Synergy Solutions</span> &copy; 2026. All rights reserved.
    </footer>

    <script>
        function togglePasswordVisibility() {
            const passInput = document.getElementById('password');
            const eyeText = document.getElementById('eyeText');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                eyeText.innerText = 'Hide';
            } else {
                passInput.type = 'password';
                eyeText.innerText = 'Show';
            }
        }

        function showLoadingState() {
            const btn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const spinner = document.getElementById('btnSpinner');

            btn.disabled = true;
            btn.classList.add('opacity-80', 'cursor-not-allowed');
            btnText.innerText = 'Signing in...';
            spinner.classList.remove('hidden');
        }
    </script>
</body>
</html>
