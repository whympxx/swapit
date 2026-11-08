@extends('layouts.app')

@section('title', 'Chat dengan ' . $other->full_name)

@section('content')
    <div class="chat-container">
        <div class="chat-header">
            <div class="chat-user-info">
                @include('partials.avatar', ['user' => $other, 'class' => 'avatar-lg'])
                <div>
                    <h3><a href="{{ route('profile.show', $other) }}">{{ $other->full_name }}</a></h3>
                    <p class="chat-deal-info">
                        <i class="fas fa-arrow-right-arrow-left"></i> {{ $offer->offeredItem->title }} ⇄ {{ $offer->wantedItem->title }}
                    </p>
                </div>
            </div>
            <div class="chat-header-actions">
                @if ($waNumber)
                    <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener" class="btn btn-success btn-sm" title="Hubungi via WhatsApp">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                @endif
                <a href="{{ route('chat.index') }}" class="btn btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </div>

        <div class="deal-banner deal-{{ $offer->status }}">
            @if ($offer->status === 'accepted')
                <span><i class="fas fa-handshake"></i> Barter sedang berjalan. Sudah saling tukar barang?</span>
                <div class="deal-actions">
                    @include('offers._action', ['route' => 'offers.complete', 'label' => 'Barter Selesai', 'icon' => 'check-double', 'class' => 'btn-primary',
                        'confirm' => 'Konfirmasi barang sudah saling ditukar?'])
                    @include('offers._action', ['route' => 'offers.cancelDeal', 'label' => 'Batalkan', 'icon' => 'ban', 'class' => 'btn-outline',
                        'confirm' => 'Batalkan barter ini? Kedua barang akan kembali tersedia.'])
                </div>
            @elseif ($offer->status === 'completed')
                <span><i class="fas fa-trophy"></i> Barter selesai 🎉 Percakapan hanya bisa dibaca.</span>
            @else
                <span><i class="fas fa-ban"></i> Barter ini dibatalkan. Percakapan hanya bisa dibaca.</span>
            @endif
        </div>

        @if ($canReview)
            <div class="chat-review">
                @include('offers._review', ['offer' => $offer, 'partner' => $other, 'open' => true])
            </div>
        @endif

        <div class="chat-messages" id="chatMessages">
            @forelse ($messages as $msg)
                <div class="message {{ $msg->sender_id === auth()->id() ? 'message-sent' : 'message-received' }}" data-message-id="{{ $msg->id }}">
                    <div class="message-content">
                        @if ($msg->sender_id !== auth()->id())
                            <strong>{{ $msg->sender->full_name }}</strong>
                        @endif
                        <p class="message-text">{{ $msg->message }}</p>
                        <span class="message-time" title="{{ $msg->created_at?->format('d M Y H:i') }}">{{ $msg->created_at?->diffForHumans() }}</span>
                    </div>
                </div>
            @empty
                <div class="chat-empty">
                    <i class="fas fa-comments"></i>
                    <p>Belum ada pesan. Mulai percakapan dan atur tempat ketemuan!</p>
                </div>
            @endforelse
        </div>

        @if ($canSend)
            <div class="chat-input-container">
                <div class="quick-replies">
                    <button type="button" class="quick-reply">Kapan bisa ketemuan?</button>
                    <button type="button" class="quick-reply">Ketemu di kampus saja ya</button>
                    <button type="button" class="quick-reply">Barangnya masih ada?</button>
                    <button type="button" class="quick-reply">Terima kasih! 🙏</button>
                </div>
                <form id="chatForm" class="chat-form" data-no-loading>
                    <textarea name="message" id="messageInput" rows="2" maxlength="2000" required
                              placeholder="Ketik pesan... (Enter untuk kirim, Shift+Enter baris baru)"></textarea>
                    <button type="submit" class="btn btn-primary" id="sendBtn" aria-label="Kirim"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const pollUrl = @js(route('chat.messages', $offer));
    const sendUrl = @js(route('chat.send', $offer));
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const canSend = @js($canSend);
    const chatMessages = document.getElementById('chatMessages');
    let lastMessageId = 0;
    let isUserScrolling = false;
    let polling = false;

    chatMessages.querySelectorAll('.message').forEach(el => {
        lastMessageId = Math.max(lastMessageId, parseInt(el.dataset.messageId || 0, 10));
    });

    function scrollToBottom(force) {
        if (force || !isUserScrolling) chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    chatMessages.addEventListener('scroll', () => {
        isUserScrolling = chatMessages.scrollTop + chatMessages.clientHeight < chatMessages.scrollHeight - 50;
    });

    function createMessageElement(msg) {
        const wrap = document.createElement('div');
        wrap.className = 'message ' + (msg.is_mine ? 'message-sent' : 'message-received') + ' message-fade-in';
        wrap.dataset.messageId = msg.id;

        const content = document.createElement('div');
        content.className = 'message-content';
        if (!msg.is_mine) {
            const name = document.createElement('strong');
            name.textContent = msg.full_name;
            content.appendChild(name);
        }
        const text = document.createElement('p');
        text.className = 'message-text';
        text.textContent = msg.message; // textContent: aman dari XSS
        content.appendChild(text);

        const time = document.createElement('span');
        time.className = 'message-time';
        time.textContent = msg.time_ago;
        content.appendChild(time);

        wrap.appendChild(content);
        return wrap;
    }

    // Polling & kirim bisa mengembalikan pesan yang sama -> tampilkan sekali saja
    function appendMessage(msg) {
        if (chatMessages.querySelector('[data-message-id="' + msg.id + '"]')) return false;
        const empty = chatMessages.querySelector('.chat-empty');
        if (empty) empty.remove();
        chatMessages.appendChild(createMessageElement(msg));
        lastMessageId = Math.max(lastMessageId, msg.id);
        return true;
    }

    function beep() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain); gain.connect(ctx.destination);
            osc.frequency.value = 800;
            gain.gain.setValueAtTime(0.3, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.1);
            osc.start(ctx.currentTime); osc.stop(ctx.currentTime + 0.1);
        } catch (e) { /* audio tidak didukung */ }
    }

    async function poll() {
        if (polling || document.hidden) return;
        polling = true;
        try {
            const res = await fetch(pollUrl + '?last_id=' + lastMessageId, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            if (data.success) {
                let fromOther = false;
                data.messages.forEach(m => { if (appendMessage(m) && !m.is_mine) fromOther = true; });
                if (data.messages.length) scrollToBottom(false);
                if (fromOther) beep();
                // Pihak lain menyelesaikan/membatalkan barter -> muat ulang tampilan
                if (canSend && data.offer_status !== 'accepted') window.location.reload();
            }
        } catch (e) {
            console.error('Gagal memuat pesan:', e);
        } finally {
            polling = false;
        }
    }

    if (canSend) {
        const form = document.getElementById('chatForm');
        const input = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');
        let sending = false;

        async function send() {
            const message = input.value.trim();
            if (!message || sending) return;
            sending = true;
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            try {
                const res = await fetch(sendUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ message })
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    input.value = '';
                    appendMessage(data.message);
                    scrollToBottom(true);
                } else {
                    alert('Gagal mengirim pesan: ' + (data.message || 'coba lagi'));
                }
            } catch (e) {
                alert('Terjadi kesalahan saat mengirim pesan');
            } finally {
                sending = false;
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
                input.focus();
            }
        }

        form.addEventListener('submit', e => { e.preventDefault(); send(); });
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) { e.preventDefault(); send(); }
        });
        document.querySelectorAll('.quick-reply').forEach(btn => btn.addEventListener('click', () => {
            input.value = btn.textContent;
            input.focus();
        }));

        input.focus();
        setInterval(poll, 3000);
        document.addEventListener('visibilitychange', () => { if (!document.hidden) poll(); });
    }

    scrollToBottom(true);
})();
</script>
@endpush
