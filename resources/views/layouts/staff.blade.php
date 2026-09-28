<!doctype html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'AfriCrew | Crew Portal & CRM' }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('images/fav.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/fav.jpg') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
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
<body id="app-body" class="h-full font-sans bg-slate-50 text-slate-900 flex min-h-screen selection:bg-amber-500 selection:text-slate-950 transition-colors duration-200 overflow-x-hidden">

    <!-- Unified CRM App Container -->
    <div class="flex w-full min-h-screen relative overflow-x-hidden">
        
        <!-- Pinned / Sticky Left SideNav Partial -->
        @include('partials.sidebar')

        <!-- Connected Main Workspace Canvas -->
        <div class="flex-1 flex flex-col min-w-0 min-h-screen bg-slate-50">
            
            <!-- Sticky Top Header Partial -->
            @include('partials.header')

            <!-- Dynamic Workspace Content Body -->
            <main class="flex-1 p-2 sm:p-3 lg:p-6 min-w-0">
                @yield('content')
            </main>


            <!-- Workspace Footer Partial -->
            @include('partials.footer')

        </div>

    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Global Theme & SweetAlert JS Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
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
    </script>
</body>
</html>

