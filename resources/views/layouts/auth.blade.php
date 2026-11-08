<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    @include('partials.head')
    <title>@yield('title') · SWAP-IT</title>
</head>
<body class="auth-page">
    <div class="auth-split">
        <aside class="auth-art">
            <a class="logo" href="{{ route('home') }}">@include('partials.logo')</a>

            <div class="auth-stage" data-parallax>
                <div class="auth-card3d c1">
                    <div class="ph"><i class="fas fa-book-open"></i></div>
                    <strong>Buku kuliah numpuk?</strong>
                    <small>Tukar aja, jangan dibuang</small>
                </div>
                <div class="auth-swap"><i class="fas fa-arrow-right-arrow-left"></i></div>
                <div class="auth-card3d c2">
                    <div class="ph"><i class="fas fa-headphones"></i></div>
                    <strong>Dapat barang incaran</strong>
                    <small>Tanpa keluar duit sepeser pun</small>
                </div>
            </div>

            <div>
                <h2>Preloved, tapi tetap fresh.</h2>
                <p>Gabung komunitas barter mahasiswa. Posting barang yang udah nggak kepakai, tukar sama yang kamu butuhin.</p>
            </div>
        </aside>

        <main class="auth-main">
            <div class="auth-card">
                <div class="auth-top">
                    <a class="logo" href="{{ route('home') }}">@include('partials.logo')</a>
                    <a class="back" href="{{ route('home') }}"><i class="fas fa-arrow-left"></i> Beranda</a>
                    <button type="button" class="theme-toggle" data-theme-toggle aria-label="Ganti tema">
                        <i class="fas fa-moon"></i><i class="fas fa-sun"></i>
                    </button>
                </div>

                <div class="auth-body">
                    @if (session('success'))
                        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <div>
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </main>
    </div>
    <script src="{{ asset('assets/js/main.js') }}?v={{ filemtime(public_path('assets/js/main.js')) }}"></script>
</body>
</html>
