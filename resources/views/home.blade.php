@extends('layouts.app')

@section('hero')
    <section class="hero-section">
        <div class="container">
            <div class="hero-grid">
                <div class="hero-content">
                    <span class="eyebrow"><span class="pulse-dot"><i class="fas fa-leaf"></i></span> Barter club #1 buat anak kampus</span>

                    <h1 class="hero-title">
                        <span class="line"><span>Tukar barang,</span></span>
                        <span class="line"><span>bukan <span class="swap-word">beli</span></span></span>
                        <span class="line"><span class="text-gradient">baru lagi.</span></span>
                    </h1>

                    <p class="hero-subtitle">
                        Buku, gadget, outfit, aksesoris — barang yang nganggur di kamarmu bisa jadi barang incaran orang lain.
                        Posting, tawar, ketemuan, done. <strong>Gratis &amp; tanpa uang.</strong>
                    </p>

                    <div class="hero-cta">
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Mulai Barter <i class="fas fa-arrow-right"></i></a>
                            <a href="#items" class="btn btn-lg"><i class="fas fa-compass"></i> Lihat Barang</a>
                        @else
                            <a href="{{ route('items.create') }}" class="btn btn-primary btn-lg"><i class="fas fa-plus"></i> Posting Barang</a>
                            <a href="#items" class="btn btn-lg"><i class="fas fa-compass"></i> Jelajahi</a>
                        @endguest
                    </div>

                    <div class="hero-proof">
                        @if ($members->isNotEmpty())
                            <div class="avatar-stack" title="Anggota terbaru">
                                @foreach ($members as $member)
                                    @include('partials.avatar', ['user' => $member])
                                @endforeach
                            </div>
                        @endif
                        <div class="hero-stats">
                            <div class="hero-stat"><div class="num" data-count="{{ $stats['items'] }}">{{ $stats['items'] }}</div><div class="label">Barang</div></div>
                            <div class="hero-stat"><div class="num" data-count="{{ $stats['users'] }}">{{ $stats['users'] }}</div><div class="label">Member</div></div>
                            <div class="hero-stat"><div class="num" data-count="{{ $stats['trades'] }}">{{ $stats['trades'] }}</div><div class="label">Barter Sukses</div></div>
                        </div>
                    </div>
                </div>

                <div class="hero-visual">
                    <div class="glow-orb"></div>
                    <div id="hero3d" data-photos='@json($heroPhotos)' aria-hidden="true"></div>

                    {{-- Cadangan bila WebGL tidak tersedia --}}
                    <div class="cube-fallback" aria-hidden="true">
                        @for ($i = 0; $i < 6; $i++)
                            <div class="face" @if (isset($heroPhotos[$i % max(1, count($heroPhotos))])) style="background-image:url('{{ $heroPhotos[$i % count($heroPhotos)] }}')" @endif></div>
                        @endfor
                    </div>

                    <div class="float-tag t1"><span class="ic"><i class="fas fa-bolt"></i></span><div>Barter instan<small>Tanpa uang, tanpa ribet</small></div></div>
                    <div class="float-tag t2"><span class="ic"><i class="fas fa-heart"></i></span><div>Simpan favorit<small>Pantau barang incaran</small></div></div>
                    <div class="float-tag t3"><span class="ic"><i class="fas fa-star"></i></span><div>Partner terpercaya<small>Rating &amp; ulasan</small></div></div>
                    <span class="hero-hint"><i class="fas fa-hand-pointer"></i> Gerakkan kursor untuk memutar</span>
                </div>
            </div>
        </div>

        <div class="marquee" aria-hidden="true">
            <div class="marquee-track">
                @for ($r = 0; $r < 2; $r++)
                    <span>
                        @foreach (\App\Models\Item::CATEGORY_ICONS as $cat => $icon)
                            {{ $cat }} <i class="fas fa-asterisk"></i>
                        @endforeach
                        Reduce <i class="fas fa-asterisk"></i> Reuse <i class="fas fa-asterisk"></i> Swap <i class="fas fa-asterisk"></i>
                    </span>
                @endfor
            </div>
        </div>
    </section>
@endsection

@section('content')
    {{-- Kategori --}}
    <section class="section">
        <div class="section-head reveal">
            <div>
                <span class="section-kicker">Kategori</span>
                <h2 class="section-title">Lagi cari apa hari ini?</h2>
            </div>
        </div>
        <div class="category-grid">
            @foreach (\App\Models\Item::CATEGORY_ICONS as $cat => $icon)
                <a href="{{ route('home', ['category' => $filters['category'] === $cat ? null : $cat]) }}#items"
                   class="category-card reveal {{ $filters['category'] === $cat ? 'active' : '' }}" data-tilt style="--d: {{ $loop->index * 60 }}ms">
                    <span class="cat-icon"><i class="fas fa-{{ $icon }}"></i></span>
                    <div>
                        <h4>{{ $cat }}</h4>
                        <span>{{ $categoryCounts[$cat] ?? 0 }} barang</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Daftar barang --}}
    <section class="section" id="items">
        <div class="section-head reveal">
            <div>
                <span class="section-kicker">Fresh drop</span>
                <h2 class="section-title">Barang siap barter <span class="count-pill">{{ $items->total() }}</span></h2>
            </div>
            @auth
                <a href="{{ route('items.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Posting Barang</a>
            @endauth
        </div>

        <div class="search-filter-section reveal">
            <form method="GET" action="{{ route('home') }}#items" class="search-form" data-no-loading>
                <div class="search-input-group">
                    <i class="fas fa-magnifying-glass"></i>
                    <input type="search" name="search" placeholder="Cari buku, hoodie, iPad…" value="{{ $filters['search'] }}" aria-label="Cari barang">
                </div>

                <select name="category" class="category-select" aria-label="Kategori">
                    <option value="">Semua Kategori</option>
                    @foreach (\App\Models\Item::CATEGORIES as $cat)
                        <option value="{{ $cat }}" @selected($filters['category'] === $cat)>{{ $cat }}</option>
                    @endforeach
                </select>

                <select name="condition" class="category-select" aria-label="Kondisi">
                    <option value="">Semua Kondisi</option>
                    @foreach (\App\Models\Item::CONDITIONS as $cond)
                        <option value="{{ $cond }}" @selected($filters['condition'] === $cond)>{{ $cond }}</option>
                    @endforeach
                </select>

                <select name="sort" class="category-select" aria-label="Urutkan">
                    @foreach ($sortOptions as $key => $label)
                        <option value="{{ $key }}" @selected($filters['sort'] === $key)>{{ $label }}</option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-primary"><i class="fas fa-sliders"></i> Terapkan</button>

                @if ($isFiltered)
                    <a href="{{ route('home') }}#items" class="btn"><i class="fas fa-xmark"></i> Reset</a>
                @endif
            </form>
        </div>

        <div class="items-grid">
            @forelse ($items as $item)
                @include('partials.item-card', [
                    'item' => $item,
                    'alreadyOffered' => isset($offeredIds[$item->id]),
                    'favorited' => isset($favoriteIds[$item->id]),
                ])
            @empty
                <div class="empty-state">
                    <i class="fas fa-ghost"></i>
                    <h3>Belum ada barang yang cocok</h3>
                    <p>{{ $isFiltered ? 'Coba ubah kata kunci atau filternya.' : 'Jadilah yang pertama posting barang!' }}</p>
                    @if ($isFiltered)
                        <a href="{{ route('home') }}#items" class="btn btn-primary"><i class="fas fa-xmark"></i> Reset Filter</a>
                    @endif
                </div>
            @endforelse
        </div>

        {{ $items->links('partials.pagination') }}
    </section>

    {{-- Cara kerja --}}
    <section class="section" id="cara-kerja">
        <div class="section-head reveal">
            <div>
                <span class="section-kicker">Cara kerja</span>
                <h2 class="section-title">4 langkah, barang baru di tangan</h2>
                <p class="section-sub">Arahkan kursor (atau tap) ke kartu untuk membaliknya.</p>
            </div>
        </div>
        <div class="steps-grid">
            @foreach ([
                ['camera', 'Posting', 'Foto barang preloved-mu, tulis kondisinya.', 'Upload foto + deskripsi singkat. Makin jujur kondisinya, makin cepat dapat tawaran.'],
                ['arrow-right-arrow-left', 'Tawar', 'Pilih barang incaran, tawarkan punyamu.', 'Satu klik untuk ajukan barter. Simpan dulu ke Favorit kalau masih galau.'],
                ['comment-dots', 'Chat & Ketemu', 'Atur waktu dan tempat COD.', 'Setelah diterima, chat langsung di aplikasi atau lewat tombol WhatsApp.'],
                ['star', 'Selesai & Ulas', 'Tandai selesai, beri bintang.', 'Ulasanmu bikin komunitas makin terpercaya dan profilmu makin kredibel.'],
            ] as [$icon, $title, $short, $long])
                <div class="flip-card reveal" tabindex="0" style="--d: {{ $loop->index * 80 }}ms">
                    <div class="flip-inner">
                        <div class="flip-face flip-front">
                            <div class="top">
                                <span class="step-num">0{{ $loop->iteration }}</span>
                                <span class="step-icon"><i class="fas fa-{{ $icon }}"></i></span>
                            </div>
                            <div>
                                <h4>{{ $title }}</h4>
                                <small>{{ $short }}</small>
                            </div>
                        </div>
                        <div class="flip-face flip-back">
                            <i class="fas fa-{{ $icon }}"></i>
                            <h4>{{ $title }}</h4>
                            <p>{{ $long }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="cta-banner reveal">
        <div>
            <h2>Kamar penuh barang nganggur?</h2>
            <p>Ubah jadi barang yang beneran kamu pakai. Satu barter = satu barang terselamatkan dari tempat sampah.</p>
            @guest
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Gabung Sekarang <i class="fas fa-arrow-right"></i></a>
            @else
                <a href="{{ route('items.create') }}" class="btn btn-primary btn-lg"><i class="fas fa-plus"></i> Posting Barang</a>
            @endguest
        </div>
        <div class="cta-shapes" aria-hidden="true">
            <div class="shape3d s1"><i class="fas fa-book-open"></i></div>
            <div class="shape3d s2"><i class="fas fa-recycle"></i></div>
            <div class="shape3d s3"><i class="fas fa-shirt"></i></div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="{{ asset('assets/js/hero3d.js') }}?v={{ filemtime(public_path('assets/js/hero3d.js')) }}"></script>
@endpush
