<nav class="navbar" data-counts-url="{{ auth()->check() ? route('api.counts') : '' }}">
    <div class="container">
        <div class="navbar-brand">
            <a href="{{ route('home') }}" aria-label="SWAP-IT beranda">@include('partials.logo')</a>
        </div>

        <div class="navbar-menu">
            <a href="{{ route('home') }}#items" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="fas fa-compass"></i> Jelajahi
            </a>

            @auth
                <a href="{{ route('items.mine') }}" class="nav-link {{ request()->routeIs('items.mine', 'items.create', 'items.edit') ? 'active' : '' }}">
                    <i class="fas fa-box-open"></i> Barangku
                </a>

                <a href="{{ route('offers.index') }}" class="nav-link {{ request()->routeIs('offers.*') ? 'active' : '' }}" data-badge="offers">
                    <i class="fas fa-handshake"></i> Penawaran
                    <span class="badge" @if (!$navCounts['offers']) style="display:none" @endif>{{ $navCounts['offers'] }}</span>
                </a>

                <a href="{{ route('chat.index') }}" class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}" data-badge="messages">
                    <i class="fas fa-comment-dots"></i> Chat
                    <span class="badge" @if (!$navCounts['messages']) style="display:none" @endif>{{ $navCounts['messages'] }}</span>
                </a>

                <a href="{{ route('favorites.index') }}" class="nav-link {{ request()->routeIs('favorites.*') ? 'active' : '' }}" title="Favorit">
                    <i class="fas fa-heart"></i> <span class="nav-label-mobile">Favorit</span>
                </a>

                <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" data-badge="notifications" title="Notifikasi">
                    <i class="fas fa-bell"></i> <span class="nav-label-mobile">Notifikasi</span>
                    <span class="badge" @if (!$navCounts['notifications']) style="display:none" @endif>{{ $navCounts['notifications'] }}</span>
                </a>

                <button type="button" class="theme-toggle" data-theme-toggle aria-label="Ganti tema terang/gelap" title="Ganti tema">
                    <i class="fas fa-moon"></i><i class="fas fa-sun"></i>
                </button>

                <div class="navbar-user">
                    <div class="dropdown">
                        <button class="dropdown-toggle" type="button" aria-haspopup="true" aria-expanded="false">
                            @include('partials.avatar', ['user' => auth()->user(), 'class' => 'avatar-sm'])
                            <span>{{ auth()->user()->username }}</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu">
                            <div class="dropdown-header">
                                @include('partials.avatar', ['user' => auth()->user()])
                                <div>
                                    <strong>{{ auth()->user()->full_name }}</strong>
                                    <span>{{ '@' . auth()->user()->username }}</span>
                                </div>
                            </div>
                            <a href="{{ route('profile') }}" class="dropdown-item"><i class="fas fa-user"></i> Profil Saya</a>
                            <a href="{{ route('items.create') }}" class="dropdown-item"><i class="fas fa-plus"></i> Posting Barang</a>
                            <a href="{{ route('favorites.index') }}" class="dropdown-item"><i class="fas fa-heart"></i> Favorit</a>
                            <a href="{{ route('settings') }}" class="dropdown-item"><i class="fas fa-gear"></i> Pengaturan</a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item danger"><i class="fas fa-arrow-right-from-bracket"></i> Keluar</button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('home') }}#cara-kerja" class="nav-link"><i class="fas fa-circle-question"></i> Cara Kerja</a>
                <button type="button" class="theme-toggle" data-theme-toggle aria-label="Ganti tema terang/gelap" title="Ganti tema">
                    <i class="fas fa-moon"></i><i class="fas fa-sun"></i>
                </button>
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Gabung Gratis <i class="fas fa-arrow-right"></i></a>
            @endauth
        </div>

        <button class="navbar-toggle" id="navbarToggle" type="button" aria-label="Buka menu">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</nav>
