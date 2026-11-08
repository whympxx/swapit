<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>@include('partials.logo')</h3>
                <p>Komunitas barter barang preloved antar mahasiswa. Hemat, sirkular, dan pastinya lebih seru daripada beli baru.</p>
            </div>

            <div class="footer-section">
                <h4>Jelajahi</h4>
                <ul>
                    <li><a href="{{ route('home') }}#items">Cari Barang</a></li>
                    <li><a href="{{ route('home') }}#cara-kerja">Cara Kerja</a></li>
                    @auth
                        <li><a href="{{ route('items.create') }}">Posting Barang</a></li>
                        <li><a href="{{ route('favorites.index') }}">Favorit Saya</a></li>
                    @else
                        <li><a href="{{ route('register') }}">Daftar Gratis</a></li>
                    @endauth
                </ul>
            </div>

            <div class="footer-section">
                <h4>Tips Barter Aman</h4>
                <ul>
                    <li>Ketemuan di area kampus yang ramai</li>
                    <li>Cek kondisi barang sebelum tukar</li>
                    <li>Tandai "Barter Selesai" &amp; kasih ulasan</li>
                </ul>
            </div>
        </div>

        <div class="footer-wordmark" aria-hidden="true">SWAP·IT</div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} SWAP-IT. Dibuat untuk mahasiswa, oleh mahasiswa.</p>
            <p>Reduce · Reuse · <strong class="text-lime">Swap</strong></p>
        </div>
    </div>
</footer>
