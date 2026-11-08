<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="theme-color" content="#07070c">
<meta name="description" content="SWAP-IT — barter barang preloved antar mahasiswa. Tukar, hemat, dan kurangi sampah.">
{{-- Pasang tema sebelum halaman digambar supaya tidak berkedip --}}
<script>
    (function () {
        var t = 'dark';
        try { t = localStorage.getItem('swapit-theme') || 'dark'; } catch (e) {}
        document.documentElement.setAttribute('data-theme', t);
        document.documentElement.classList.add('js');
    })();
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ filemtime(public_path('assets/css/style.css')) }}">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='9' fill='%23c8ff3d'/><path d='M9 12h12l-3-3M23 20H11l3 3' stroke='%230a0a0f' stroke-width='2.6' fill='none' stroke-linecap='round' stroke-linejoin='round'/></svg>">
