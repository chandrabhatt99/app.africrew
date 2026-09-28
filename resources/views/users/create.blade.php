<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Information - Create User</title>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f8fa;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 sm:p-8">

    <!-- Standalone Centered Form Container -->
    <div class="w-full max-w-[1050px] bg-white border border-[#e2e8f0] rounded-2xl shadow-sm p-6 sm:p-10 my-auto">

        <!-- Form Title & Subtitle Header -->
        <div class="border-b border-slate-100 pb-6 mb-8">
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">User Information</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Enter the user details below.</p>
        </div>

        <!-- Success Toast Notification Alert -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm font-semibold flex items-center justify-between mb-8 shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-black shrink-0">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold text-xs">Dismiss</button>
            </div>
        @endif

        <!-- User Information Form -->
        <form id="user-create-form" method="POST" action="{{ route('users.store') }}" class="space-y-8" onsubmit="handleFormSubmit(event)">
            @csrf

            <!-- 2-Column Desktop Grid / 1-Column Mobile Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- 1. Full Name -->
                <div>
                    <label for="name" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                        Full Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Enter full name" required
                        class="w-full bg-white border border-[#d9dee5] focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 rounded-lg px-4 h-11 sm:h-12 text-slate-900 text-sm font-medium transition-all shadow-2xs outline-none @error('name') border-rose-500 bg-rose-50/20 @enderror">
                    @error('name')
                        <p class="text-xs text-rose-600 font-medium mt-1 flex items-center gap-1">
                            <span>⚠️</span> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- 2. Email Address -->
                <div>
                    <label for="email" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                        Email Address <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Enter email address" required
                        class="w-full bg-white border border-[#d9dee5] focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 rounded-lg px-4 h-11 sm:h-12 text-slate-900 text-sm font-medium transition-all shadow-2xs outline-none @error('email') border-rose-500 bg-rose-50/20 @enderror">
                    @error('email')
                        <p class="text-xs text-rose-600 font-medium mt-1 flex items-center gap-1">
                            <span>⚠️</span> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- 3. Phone Number -->
                <div>
                    <label for="phone" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                        Phone Number
                    </label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Enter phone number"
                        class="w-full bg-white border border-[#d9dee5] focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 rounded-lg px-4 h-11 sm:h-12 text-slate-900 text-sm font-medium transition-all shadow-2xs outline-none @error('phone') border-rose-500 bg-rose-50/20 @enderror">
                    @error('phone')
                        <p class="text-xs text-rose-600 font-medium mt-1 flex items-center gap-1">
                            <span>⚠️</span> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- 4. Gender -->
                <div>
                    <label for="gender" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                        Gender
                    </label>
                    <select id="gender" name="gender"
                        class="w-full bg-white border border-[#d9dee5] focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 rounded-lg px-4 h-11 sm:h-12 text-slate-900 text-sm font-medium transition-all shadow-2xs outline-none @error('gender') border-rose-500 bg-rose-50/20 @enderror">
                        <option value="">Select Gender</option>
                        <option value="Male" @selected(old('gender') == 'Male')>Male</option>
                        <option value="Female" @selected(old('gender') == 'Female')>Female</option>
                        <option value="Other" @selected(old('gender') == 'Other')>Other</option>
                    </select>
                    @error('gender')
                        <p class="text-xs text-rose-600 font-medium mt-1 flex items-center gap-1">
                            <span>⚠️</span> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- 5. Date of Birth -->
                <div>
                    <label for="date_of_birth" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                        Date of Birth
                    </label>
                    <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}"
                        class="w-full bg-white border border-[#d9dee5] focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 rounded-lg px-4 h-11 sm:h-12 text-slate-900 text-sm font-medium transition-all shadow-2xs outline-none @error('date_of_birth') border-rose-500 bg-rose-50/20 @enderror">
                    @error('date_of_birth')
                        <p class="text-xs text-rose-600 font-medium mt-1 flex items-center gap-1">
                            <span>⚠️</span> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- 6. Account Status -->
                <div>
                    <label for="status" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">
                        Account Status <span class="text-rose-500">*</span>
                    </label>
                    <select id="status" name="status" required
                        class="w-full bg-white border border-[#d9dee5] focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 rounded-lg px-4 h-11 sm:h-12 text-slate-900 text-sm font-medium transition-all shadow-2xs outline-none @error('status') border-rose-500 bg-rose-50/20 @enderror">
                        <option value="active" @selected(old('status', 'active') == 'active')>Active</option>
                        <option value="inactive" @selected(old('status') == 'inactive')>Inactive</option>
                    </select>
                    @error('status')
                        <p class="text-xs text-rose-600 font-medium mt-1 flex items-center gap-1">
                            <span>⚠️</span> {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

            <!-- Form Footer Action Buttons -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between gap-4">
                <!-- Secondary Cancel Button (Left) -->
                <a href="javascript:history.back()"
                    class="px-6 py-2.5 sm:py-3 rounded-lg border border-[#d9dee5] bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm transition-all shadow-2xs">
                    Cancel
                </a>

                <!-- Primary Save User Button (Right) -->
                <button type="submit" id="submit-btn"
                    class="px-8 py-2.5 sm:py-3 rounded-lg bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold text-sm transition-all shadow-sm flex items-center gap-2 cursor-pointer">
                    <span id="btn-text">Save User</span>
                    <svg id="btn-spinner" class="animate-spin h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>

        </form>

    </div>

    <!-- Client-Side Form Loading State Handler -->
    <script>
        function handleFormSubmit(event) {
            const btn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');
            const btnSpinner = document.getElementById('btn-spinner');

            if (btn && btnText && btnSpinner) {
                btn.disabled = true;
                btn.classList.add('opacity-80', 'cursor-not-allowed');
                btnText.innerText = 'Saving...';
                btnSpinner.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>
