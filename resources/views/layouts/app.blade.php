<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    @include('partials.head')
    <title>@hasSection('title')@yield('title') · SWAP-IT @else SWAP-IT — Barter Barang Preloved Antar Mahasiswa @endif</title>
</head>
<body>
    @include('partials.navbar')
    @include('partials.flash')

    @yield('hero')

    <main class="container main-content">
        @yield('content')
    </main>

    @include('partials.footer')

    <script src="{{ asset('assets/js/main.js') }}?v={{ filemtime(public_path('assets/js/main.js')) }}"></script>
    @stack('scripts')
</body>
</html>
