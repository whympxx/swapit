// SWAP-IT Main JavaScript (v2)

document.addEventListener('DOMContentLoaded', function () {
    setupTheme();
    setupNavbar();
    setupDropdowns();
    setupSmoothScroll();
    setupAlerts();
    setupTabs();
    setupImagePreview('photo', 'imagePreview');
    setupDropzone();
    setupPasswordToggles();
    setupPasswordStrength();
    setupCharCounters();
    setupLoadingButtons();
    setupBadgePolling();
    setupFavorites();
    setupShare();
    setupFlipCards();
    initReveal();
    initCountUp();
    initTilt();
    initParallax();
    initScrollProgress();
    initRipple();
    document.body.classList.add('loaded');
});

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const csrfToken = () => (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

// ===== Tema terang / gelap =====
function setupTheme() {
    document.querySelectorAll('[data-theme-toggle]').forEach(btn => {
        btn.addEventListener('click', () => {
            const next = document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', next);
            try { localStorage.setItem('swapit-theme', next); } catch (e) { /* mode privat */ }
            const meta = document.querySelector('meta[name="theme-color"]');
            if (meta) meta.content = next === 'light' ? '#f4f3fa' : '#07070c';
            document.dispatchEvent(new CustomEvent('themechange', { detail: next }));
        });
    });
}

// ===== Navbar mobile =====
function setupNavbar() {
    const toggle = document.getElementById('navbarToggle');
    const menu = document.querySelector('.navbar-menu');
    if (!toggle || !menu) return;

    const setIcon = open => { toggle.innerHTML = open ? '<i class="fas fa-xmark"></i>' : '<i class="fas fa-bars"></i>'; };
    const close = () => { menu.classList.remove('active'); setIcon(false); };

    toggle.addEventListener('click', e => {
        e.stopPropagation();
        setIcon(menu.classList.toggle('active'));
    });
    document.addEventListener('click', e => { if (!e.target.closest('.navbar')) close(); });
    menu.querySelectorAll('a.nav-link, a.btn').forEach(link => {
        link.addEventListener('click', () => { if (window.innerWidth <= 960) close(); });
    });

    let t;
    window.addEventListener('resize', () => {
        clearTimeout(t);
        t = setTimeout(() => { if (window.innerWidth > 960) close(); }, 200);
    });
}

// ===== Dropdown pengguna =====
function setupDropdowns() {
    document.querySelectorAll('.dropdown').forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        if (!toggle) return;
        toggle.addEventListener('click', e => {
            e.preventDefault();
            e.stopPropagation();
            const open = dropdown.classList.toggle('open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    });
    document.addEventListener('click', e => {
        document.querySelectorAll('.dropdown.open').forEach(d => {
            if (!d.contains(e.target)) {
                d.classList.remove('open');
                const t = d.querySelector('.dropdown-toggle');
                if (t) t.setAttribute('aria-expanded', 'false');
            }
        });
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') document.querySelectorAll('.dropdown.open').forEach(d => d.classList.remove('open'));
    });
}

// ===== Smooth scroll (abaikan href="#") =====
function setupSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (!href || href === '#') return;
            let target = null;
            try { target = document.querySelector(href); } catch (err) { return; }
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'start' });
            }
        });
    });
}

// ===== Alert hilang otomatis =====
function setupAlerts() {
    document.querySelectorAll('.alert').forEach(alert => {
        if (alert.hasAttribute('data-persist') || alert.classList.contains('alert-error')) return;
        setTimeout(() => {
            alert.style.transition = 'opacity .4s, transform .4s';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-8px)';
            setTimeout(() => alert.remove(), 400);
        }, 5000);
    });
}

// ===== Tabs =====
function setupTabs() {
    const buttons = document.querySelectorAll('.tab-btn');
    const contents = document.querySelectorAll('.tab-content');
    buttons.forEach(button => {
        button.addEventListener('click', function () {
            buttons.forEach(b => b.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            const target = document.getElementById(this.dataset.tab + '-tab');
            if (target) target.classList.add('active');
            try {
                const url = new URL(window.location.href);
                url.searchParams.set('tab', this.dataset.tab);
                history.replaceState(null, '', url);
            } catch (e) { /* abaikan */ }
        });
    });
}

// ===== Preview foto + validasi =====
function setupImagePreview(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    if (!input || !preview) return;

    input.addEventListener('change', function (e) {
        const file = e.target.files[0];
        preview.innerHTML = '';
        preview.style.display = 'none';
        const label = document.querySelector('[data-dropzone-label]');
        if (!file) { if (label) label.textContent = label.dataset.default || ''; return; }

        if (file.size > 5 * 1024 * 1024) {
            showToast('Ukuran file terlalu besar. Maksimal 5MB.', 'error');
            input.value = '';
            return;
        }
        if (!/^image\/(jpeg|png|gif|webp)$/.test(file.type)) {
            showToast('Hanya JPG, PNG, GIF, atau WEBP.', 'error');
            input.value = '';
            return;
        }
        if (label) label.textContent = file.name;

        const reader = new FileReader();
        reader.onload = ev => {
            const img = document.createElement('img');
            img.src = ev.target.result;
            img.alt = 'Preview';
            preview.appendChild(img);
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    });
}

function setupDropzone() {
    document.querySelectorAll('.dropzone').forEach(zone => {
        ['dragenter', 'dragover'].forEach(ev => zone.addEventListener(ev, () => zone.classList.add('dragover')));
        ['dragleave', 'drop'].forEach(ev => zone.addEventListener(ev, () => zone.classList.remove('dragover')));
    });
}

// ===== Password =====
function setupPasswordToggles() {
    document.querySelectorAll('.password-toggle').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = document.getElementById(this.dataset.target);
            if (!input) return;
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            this.innerHTML = show ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
            this.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        });
    });
}

function setupPasswordStrength() {
    document.querySelectorAll('input[data-strength]').forEach(input => {
        const output = document.getElementById(input.dataset.strength);
        if (!output) return;
        input.addEventListener('input', function () {
            const pw = this.value;
            let score = 0;
            if (pw.length >= 6) score++;
            if (pw.length >= 10) score++;
            if (/[a-z]/.test(pw) && /[A-Z]/.test(pw)) score++;
            if (/[0-9]/.test(pw)) score++;
            if (/[^a-zA-Z0-9]/.test(pw)) score++;

            if (!pw) { output.textContent = ''; output.className = 'password-strength'; }
            else if (score <= 1) { output.textContent = 'Lemah'; output.className = 'password-strength weak'; }
            else if (score <= 3) { output.textContent = 'Sedang'; output.className = 'password-strength medium'; }
            else { output.textContent = 'Kuat 💪'; output.className = 'password-strength strong'; }
        });
    });
}

// ===== Penghitung karakter =====
function setupCharCounters() {
    document.querySelectorAll('[data-counter]').forEach(field => {
        const counter = document.getElementById(field.dataset.counter);
        const max = parseInt(field.getAttribute('maxlength') || '0', 10);
        if (!counter || !max) return;
        const update = () => { counter.textContent = field.value.length + ' / ' + max; };
        field.addEventListener('input', update);
        update();
    });
}

// ===== Tombol submit: status loading =====
function setupLoadingButtons() {
    document.querySelectorAll('form:not([data-no-loading])').forEach(form => {
        form.addEventListener('submit', function (e) {
            if (e.defaultPrevented) return;   // confirm() dibatalkan
            const btn = this.querySelector('button[type="submit"]');
            if (btn && !btn.disabled) {
                btn.dataset.originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            }
        });
    });
    window.addEventListener('pageshow', () => {
        document.querySelectorAll('button[data-original-html]').forEach(btn => {
            btn.disabled = false;
            btn.innerHTML = btn.dataset.originalHtml;
        });
    });
}

// ===== Badge navbar diperbarui berkala =====
function setupBadgePolling() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;
    let url = navbar.dataset.countsUrl || '';
    if (!url && navbar.dataset.loggedIn === '1' && navbar.dataset.baseUrl) {
        url = navbar.dataset.baseUrl + 'api/get_unread_count.php';
    }
    if (!url) return;

    const setBadge = (name, count) => {
        const badge = document.querySelector('.nav-link[data-badge="' + name + '"] .badge');
        if (!badge) return;
        badge.textContent = count;
        badge.style.display = count > 0 ? '' : 'none';
    };

    async function refresh() {
        if (document.hidden) return;
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();
            if (!data.success) return;
            setBadge('messages', data.messages_count);
            setBadge('offers', data.offers_count);
            setBadge('notifications', data.notifications_count);
        } catch (e) { /* offline */ }
    }
    setInterval(refresh, 15000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) refresh(); });
}

// ===== Favorit (tombol hati) =====
function setupFavorites() {
    document.querySelectorAll('[data-fav-url]').forEach(btn => {
        btn.addEventListener('click', async e => {
            e.preventDefault();
            e.stopPropagation();
            if (btn.dataset.busy) return;
            btn.dataset.busy = '1';

            // Optimistis: langsung ubah tampilan
            const wasActive = btn.classList.contains('active');
            btn.classList.toggle('active', !wasActive);
            btn.classList.remove('pop'); void btn.offsetWidth; btn.classList.add('pop');

            try {
                const res = await fetch(btn.dataset.favUrl, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken() }
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.message || 'Gagal');
                btn.classList.toggle('active', data.favorited);
                btn.setAttribute('aria-pressed', data.favorited ? 'true' : 'false');
                btn.setAttribute('aria-label', data.favorited ? 'Hapus dari favorit' : 'Simpan ke favorit');
                document.querySelectorAll('[data-fav-count]').forEach(el => { el.textContent = data.count; });
                if (data.favorited) burstHearts(btn);
                showToast(data.message, 'success');

                // Di halaman Favorit, kartu yang di-unfavorite ikut hilang
                if (!data.favorited && btn.closest('[data-favorites-page]')) {
                    const card = btn.closest('.item-card');
                    if (card) {
                        card.style.transition = 'opacity .4s, transform .4s';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(.9)';
                        setTimeout(() => card.remove(), 400);
                    }
                }
            } catch (err) {
                btn.classList.toggle('active', wasActive);
                showToast(err.message || 'Gagal menyimpan favorit', 'error');
            } finally {
                delete btn.dataset.busy;
            }
        });
    });
}

function burstHearts(origin) {
    if (prefersReducedMotion) return;
    const rect = origin.getBoundingClientRect();
    for (let i = 0; i < 8; i++) {
        const h = document.createElement('i');
        h.className = 'fas fa-heart';
        const angle = (Math.PI * 2 * i) / 8;
        Object.assign(h.style, {
            position: 'fixed',
            left: rect.left + rect.width / 2 + 'px',
            top: rect.top + rect.height / 2 + 'px',
            color: i % 2 ? '#ff4ecd' : '#c8ff3d',
            fontSize: '12px',
            pointerEvents: 'none',
            zIndex: 4000,
            transition: 'transform .7s cubic-bezier(.22,1,.36,1), opacity .7s',
        });
        document.body.appendChild(h);
        requestAnimationFrame(() => {
            h.style.transform = `translate(${Math.cos(angle) * 46}px, ${Math.sin(angle) * 46}px) scale(1.3)`;
            h.style.opacity = '0';
        });
        setTimeout(() => h.remove(), 750);
    }
}

// ===== Bagikan barang =====
function setupShare() {
    document.querySelectorAll('[data-share]').forEach(btn => {
        btn.addEventListener('click', async () => {
            const data = { title: btn.dataset.shareTitle || document.title, url: btn.dataset.share };
            if (navigator.share) {
                try { await navigator.share(data); } catch (e) { /* dibatalkan */ }
                return;
            }
            try {
                await navigator.clipboard.writeText(data.url);
                showToast('Link disalin ke clipboard 🔗', 'success');
            } catch (e) {
                prompt('Salin link ini:', data.url);
            }
        });
    });
}

// ===== Kartu flip (tap di layar sentuh / keyboard) =====
function setupFlipCards() {
    document.querySelectorAll('.flip-card').forEach(card => {
        card.addEventListener('click', () => card.classList.toggle('flipped'));
        card.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); card.classList.toggle('flipped'); }
        });
    });
}

// ===== Animasi masuk saat scroll =====
function initReveal() {
    const els = document.querySelectorAll('.reveal');
    if (!('IntersectionObserver' in window) || prefersReducedMotion) {
        els.forEach(el => el.classList.add('animate-in'));
        return;
    }
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                el.classList.add('animate-in');
                observer.unobserve(el);
                // Setelah animasi selesai, lepas kelasnya agar efek tilt tidak ikut tertunda
                const delay = parseInt(getComputedStyle(el).getPropertyValue('--d'), 10) || 0;
                setTimeout(() => el.classList.remove('reveal', 'animate-in'), 950 + delay);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
    els.forEach(el => observer.observe(el));

    // Pengaman: elemen yang sudah terlihat di layar tapi belum teranimasi (mis. browser
    // sedang sibuk) tetap ditampilkan.
    setTimeout(() => {
        document.querySelectorAll('.reveal:not(.animate-in)').forEach(el => {
            if (el.getBoundingClientRect().top < window.innerHeight) el.classList.add('animate-in');
        });
    }, 2500);
}

// ===== Angka menghitung naik =====
function initCountUp() {
    const els = document.querySelectorAll('[data-count]');
    if (!els.length) return;
    const run = el => {
        const target = parseInt(el.dataset.count, 10) || 0;
        if (prefersReducedMotion || target === 0) { el.textContent = target; return; }
        const start = performance.now();
        const dur = 1400;
        const tick = now => {
            const p = Math.min(1, Math.max(0, (now - start) / dur));
            el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3)));
            if (p < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    };
    if (!('IntersectionObserver' in window)) { els.forEach(run); return; }
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) { run(e.target); io.unobserve(e.target); } });
    });
    els.forEach(el => io.observe(el));
}

// ===== Tilt 3D + kilau =====
function initTilt() {
    if (prefersReducedMotion || window.matchMedia('(hover: none)').matches) return;
    document.querySelectorAll('[data-tilt]').forEach(el => {
        const max = parseFloat(el.dataset.tilt) || 10;
        const glare = document.createElement('span');
        glare.className = 'tilt-glare';
        el.appendChild(glare);
        let raf = null;

        el.addEventListener('pointermove', e => {
            const rect = el.getBoundingClientRect();
            const px = (e.clientX - rect.left) / rect.width;
            const py = (e.clientY - rect.top) / rect.height;
            if (raf) cancelAnimationFrame(raf);
            raf = requestAnimationFrame(() => {
                el.style.transition = 'transform .1s linear, border-color .3s, box-shadow .5s';
                el.style.transform = `perspective(900px) rotateX(${(0.5 - py) * max}deg) rotateY(${(px - 0.5) * max}deg) translateY(-6px)`;
                el.style.setProperty('--gx', px * 100 + '%');
                el.style.setProperty('--gy', py * 100 + '%');
            });
        });
        el.addEventListener('pointerleave', () => {
            if (raf) cancelAnimationFrame(raf);
            el.style.transition = '';
            el.style.transform = '';
        });
    });
}

// ===== Paralaks 3D mengikuti kursor (halaman login/daftar) =====
function initParallax() {
    if (prefersReducedMotion) return;
    document.querySelectorAll('[data-parallax]').forEach(stage => {
        const scope = stage.parentElement;
        stage.style.transition = 'transform .3s ease-out';
        stage.style.transformStyle = 'preserve-3d';
        scope.addEventListener('pointermove', e => {
            const r = scope.getBoundingClientRect();
            const x = (e.clientX - r.left) / r.width - 0.5;
            const y = (e.clientY - r.top) / r.height - 0.5;
            stage.style.transform = `perspective(1000px) rotateY(${x * 16}deg) rotateX(${-y * 16}deg)`;
        });
        scope.addEventListener('pointerleave', () => { stage.style.transform = ''; });
    });
}

function initScrollProgress() {
    const bar = document.createElement('div');
    bar.className = 'scroll-progress';
    document.body.appendChild(bar);
    window.addEventListener('scroll', () => {
        const h = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        bar.style.width = (h > 0 ? (window.pageYOffset / h) * 100 : 0) + '%';
    }, { passive: true });
}

function initRipple() {
    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('click', function (event) {
            const rect = button.getBoundingClientRect();
            const d = Math.max(button.clientWidth, button.clientHeight);
            const ripple = document.createElement('span');
            ripple.style.width = ripple.style.height = d + 'px';
            ripple.style.left = (event.clientX - rect.left - d / 2) + 'px';
            ripple.style.top = (event.clientY - rect.top - d / 2) + 'px';
            ripple.classList.add('ripple');
            const old = button.querySelector('.ripple');
            if (old) old.remove();
            button.appendChild(ripple);
        });
    });
}

// ===== Toast =====
function showToast(message, type = 'success') {
    document.querySelectorAll('.toast').forEach(t => t.remove());
    const toast = document.createElement('div');
    toast.className = 'toast toast-' + type;
    toast.setAttribute('role', 'status');
    const icon = document.createElement('i');
    icon.className = 'fas fa-' + (type === 'success' ? 'circle-check' : 'circle-exclamation');
    const text = document.createElement('span');
    text.textContent = message;
    toast.append(icon, text);
    document.body.appendChild(toast);
    requestAnimationFrame(() => toast.classList.add('show'));
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 350);
    }, 2800);
}
