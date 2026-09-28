<!doctype html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'AfriCrew | Premium Event Crewing & On-Site Crew' }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('images/fav.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/fav.jpg') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        gold: {
                            400: '#F5D061',
                            500: '#F59E0B',
                            600: '#D97706',
                            700: '#B45309',
                        }
                    }
                }
            }
        }
    </script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body id="app-body" class="h-full font-sans bg-slate-50 text-slate-900 flex flex-col selection:bg-gold-500 selection:text-slate-950 transition-colors duration-200">

    <!-- Separate Header Partial -->
    @include('partials.header')

    <!-- Main Content Area -->
    <main class="flex-grow min-w-0">
        @yield('content')
    </main>

    <!-- Separate Footer Partial -->
    @include('partials.crew-profile-modal')
    @include('partials.multi-crew-basket')
    @include('partials.footer')

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Global Theme & SweetAlert JS Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            localStorage.removeItem('africrew_theme');
            document.body.classList.remove('dark', 'bg-slate-950', 'text-white');
            document.body.classList.add('bg-slate-50', 'text-slate-900');

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#F59E0B',
                    timer: 4000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Notice',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#F59E0B'
                });
            @endif

            @if(session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Attention',
                    text: "{{ session('warning') }}",
                    confirmButtonColor: '#F59E0B'
                });
            @endif

            @if(session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Information',
                    text: "{{ session('info') }}",
                    confirmButtonColor: '#F59E0B'
                });
            @endif

            @if(isset($errors) && $errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Please check your inputs',
                    html: '<ul class="text-left text-xs text-rose-600 font-semibold space-y-1">@foreach($errors->all() as $err)<li>• {{ addslashes($err) }}</li>@endforeach</ul>',
                    confirmButtonColor: '#F59E0B'
                });
            @endif
        });

        function toggleSidebar() {
            const drawer = document.getElementById('mobile-sidebar-drawer');
            if (drawer) drawer.classList.toggle('hidden');
        }

        function closeSidebarDrawer() {
            const drawer = document.getElementById('mobile-sidebar-drawer');
            if (drawer) drawer.classList.add('hidden');
        }
    </script>
</body>
</html>
