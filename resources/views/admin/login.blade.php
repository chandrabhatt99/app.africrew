<!doctype html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Portal Login | AfriCrew</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        gold: {
                            400: '#F5D061',
                            500: '#E5B84B',
                            600: '#D97706',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full font-sans bg-slate-50 text-slate-900 flex flex-col justify-center items-center relative selection:bg-gold-500 selection:text-slate-950">

    <!-- Ambient Gradients -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-amber-200/40 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-yellow-200/40 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md px-6 py-12 relative z-10">
        
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 group transition-transform duration-300 hover:scale-105">
                <img src="{{ asset('images/africrew_logo.jpg') }}" alt="AfriCrew - Crew Connect Hub" class="h-12 w-auto object-contain">
            </a>
            <p class="text-xs uppercase tracking-widest font-extrabold text-amber-700 mt-2">Administrative Gateway</p>
        </div>

        <!-- Demo Account Shortcut Box
        <div class="mb-6 bg-slate-900 text-white rounded-2xl p-4 shadow-md border border-slate-800 flex items-center justify-between">
            <div>
                <div class="text-xs font-bold text-gold-400">Admin Demo Credentials</div>
                <div class="text-xs text-slate-300 mt-0.5">admin@africrew.com · password</div>
            </div>
            <button type="button" onclick="fillAdmin()" class="px-3 py-1.5 rounded-xl bg-amber-500 text-slate-950 font-black text-xs hover:brightness-110">
                Auto Fill
            </button>
        </div> -->

        <!-- Form Card Container -->
        <div class="bg-white border border-slate-200/90 rounded-3xl p-8 md:p-10 shadow-xl shadow-slate-200/60 relative overflow-hidden">
            
            <!-- Gold Accent Line -->
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-400 via-amber-500 to-yellow-500"></div>

            <div class="mb-8 text-center">
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Admin System Sign In</h1>
                <p class="text-sm text-slate-500 mt-1">Enter your credentials to access operations management</p>
            </div>

            <!-- Status Session Alert -->
            @if(session('status'))
                <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- General Error Alert -->
            @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <span class="font-bold block mb-0.5 text-rose-900">Authentication Failed</span>
                        <span class="text-xs text-rose-700">{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-6">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input id="email" 
                               name="email" 
                               type="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus
                               placeholder="admin@africrew.com"
                               class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all duration-200" />
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input id="password" 
                               name="password" 
                               type="password" 
                               required 
                               placeholder="••••••••••••"
                               class="w-full pl-11 pr-12 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all duration-200" />
                        <button type="button" 
                                onclick="togglePasswordVisibility()" 
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-700 transition-colors focus:outline-none"
                                title="Toggle password visibility">
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" name="remember" class="sr-only peer">
                        <div class="w-5 h-5 rounded-lg border border-slate-300 bg-slate-50 peer-checked:bg-slate-900 peer-checked:border-slate-900 transition-all flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-gold-400 opacity-0 peer-checked:opacity-100 transition-opacity font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-600 group-hover:text-slate-900 transition-colors">Remember my session</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-4 px-6 bg-slate-900 text-gold-400 font-extrabold text-sm tracking-wide rounded-2xl shadow-lg shadow-slate-900/10 hover:bg-slate-800 hover:shadow-slate-900/20 hover:scale-[1.01] active:scale-[0.99] transition-all duration-200 flex items-center justify-center gap-2 group">
                    <span>Access Admin Dashboard</span>
                    <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <a href="{{ route('staff.login') }}" class="text-xs font-bold text-amber-700 hover:text-amber-800 hover:underline">
                    Looking for Staff & Usher Login Portal? Click Here →
                </a>
            </div>
        </div>

        <p class="text-center text-xs text-slate-500 mt-8">
            &copy; {{ date('Y') }} AfriCrew operations portal.
        </p>
    </div>

    <script>
        function fillAdmin() {
            document.getElementById('email').value = 'admin@africrew.com';
            document.getElementById('password').value = 'password';
        }

        function togglePasswordVisibility() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.016 10.016 0 013.682-.763c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m-1.782 1.782l-1.41-1.41M3 3l18 18"></path>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }
    </script>
</body>
</html>
