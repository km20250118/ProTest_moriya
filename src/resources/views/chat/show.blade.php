<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ($isBuyer ? $seller : $buyer)->name }}さんとの取引</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<!-- ═══ COACHTECHヘッダー ═══ -->
<header style="background: #000; color: #fff; padding: 12px 24px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 1000;">
    <div style="display: flex; align-items: center; gap: 24px;">
        <a href="/" style="display: flex; align-items: center;">
    <img src="{{ asset('img/logo.png') }}" alt="COACHTECH" style="height: 28px;">
　　　　　</a>
        <form action="/item" method="GET" style="margin: 0;">
            <input type="text" 
                   name="keyword" 
                   placeholder="なにをお探しですか？" 
                   style="width: 400px; padding: 6px 12px; border: none; border-radius: 4px; font-size: 13px;"
                   value="{{ request('keyword') }}">
        </form>
    </div>
    <nav style="display: flex; gap: 20px; align-items: center;">
    <form action="/logout" method="POST" style="margin: 0;">
        @csrf
        <button type="submit" style="background: none; border: none; color: #fff; cursor: pointer; font-size: 14px; padding: 0; font-family: inherit;">ログアウト</button>
    </form>
    <a href="/mypage" style="color: #fff; text-decoration: none; font-size: 14px;">マイページ</a>
    <a href="/sell" style="background: #fff; color: #000; padding: 6px 16px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: 600;">出品</a>
　　</nav>
</header>

<style>
/* リセット */
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }

/* ═══════════════════════════════════════════
   レイアウト
   ═══════════════════════════════════════════ */
.chat-layout {
    display: flex;
    height: calc(100vh - 48px);
}

/* ═══════════════════════════════════════════
   サイドバー（ダーク・テキストのみ）
   ═══════════════════════════════════════════ */
.chat-sidebar {
    width: 180px;
    min-width: 180px;
    background: #3d3d3d;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.chat-sidebar-header {
    padding: 14px 16px;
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    border-bottom: 1px solid rgba(255,255,255,0.12);
    flex-shrink: 0;
}
.chat-sidebar-list {
    flex: 1;
    overflow-y: auto;
}
.chat-sidebar-item {
    display: block;
    padding: 14px 16px;
    color: #fff;
    font-size: 13px;
    text-decoration: none;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    cursor: pointer;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    transition: background 0.15s;
}
.chat-sidebar-item:hover {
    background: rgba(255,255,255,0.08);
}

/* ═══════════════════════════════════════════
   メインエリア
   ═══════════════════════════════════════════ */
.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #fff;
}

/* ─── ヘッダー（アバター＋タイトル＋ボタン） ─── */
.chat-main-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 20px;
    background: #fff;
    border-bottom: 1px solid #eee;
    flex-shrink: 0;
    gap: 12px;
}
.chat-header-left {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
    flex: 1;
}
.chat-header-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #c8c8c8;
    flex-shrink: 0;
}
.chat-header-title {
    font-size: 15px;
    color: #333;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* ─── 取引を完了するボタン（コーラルピル） ─── */
.btn-complete {
    background: #f87171;
    color: #fff;
    border: none;
    padding: 8px 20px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    white-space: nowrap;
    transition: background 0.2s;
    flex-shrink: 0;
    font-family: inherit;
}
.btn-complete:hover { background: #ef4444; }

/* ─── 商品情報バー（画像＋名＋価格） ─── */
.chat-item-card {
    display: flex;
    align-items: center;
    padding: 16px 20px;
    gap: 18px;
    border-bottom: 1px solid #e8e8e8;
    background: #fff;
    flex-shrink: 0;
}
.chat-item-card .item-img {
    width: 78px;
    height: 78px;
    background: #d0d0d0;
    flex-shrink: 0;
    object-fit: cover;
    display: block;
}
.chat-item-card .item-card-name {
    font-size: 18px;
    font-weight: 700;
    color: #222;
}
.chat-item-card .item-card-price {
    font-size: 14px;
    color: #555;
    margin-top: 3px;
}

/* ═══════════════════════════════════════════
   メッセージ一覧（名前バー＋メッセージ枠）
   ═══════════════════════════════════════════ */
.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 24px 20px;
    display: flex;
    flex-direction: column;
    gap: 22px;
    background: #fff;
}

/* 1メッセージ = [名前行] + [メッセージ枠] + [アクション] */
.chat-msg {
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.chat-msg.self { align-items: flex-end; }

/* 名前＋アバターの行 */
.chat-msg-header {
    display: flex;
    align-items: center;
    gap: 7px;
}
.chat-msg.self .chat-msg-header { flex-direction: row-reverse; }

.chat-msg-avatar {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #c8c8c8;
    flex-shrink: 0;
}
.chat-msg-name {
    font-size: 12px;
    color: #666;
}

/* メッセージ枠（グレーバー） */
.chat-msg-bar {
    background: #e4e4e4;
    padding: 10px 14px;
    font-size: 13px;
    color: #333;
    max-width: 72%;
    word-break: break-word;
    white-space: pre-wrap;
    line-height: 1.5;
    min-width: 40px;
}
.chat-msg.self .chat-msg-bar { background: #d8d8d8; }

/* メッセージ内の画像添付 */
.chat-msg-bar .msg-image {
    max-width: 160px;
    border-radius: 4px;
    margin-top: 6px;
    display: block;
    cursor: zoom-in;
    border: 1px solid rgba(0,0,0,0.08);
}

/* 編集・削除リンク */
.chat-msg-actions {
    display: flex;
    gap: 8px;
    font-size: 11px;
}
.chat-msg-actions a,
.chat-msg-actions button {
    color: #999;
    text-decoration: none;
    background: none;
    border: none;
    cursor: pointer;
    font-family: inherit;
    font-size: 11px;
    padding: 0;
    line-height: 1;
}
.chat-msg-actions a:hover,
.chat-msg-actions button:hover { color: #555; }
.chat-msg-actions .delete-form { display: inline; }

/* ═══════════════════════════════════════════
   入力エリア（シングルラインインプット＋画像を追加＋送信アイコン）
   ═══════════════════════════════════════════ */
.chat-input-area {
    padding: 12px 16px;
    border-top: 1px solid #e8e8e8;
    background: #fff;
    flex-shrink: 0;
}
.chat-input-area .error-msg {
    color: #dc3545;
    font-size: 12px;
    margin-bottom: 6px;
}
.chat-input-row {
    display: flex;
    align-items: center;
    gap: 8px;
}

/* テキスト入力 */
.chat-text-input {
    flex: 1;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 8px 12px;
    font-size: 13px;
    font-family: inherit;
    outline: none;
    min-width: 0;
    box-sizing: border-box;
    background: #fff;
    color: #333;
    height: 38px;
}
.chat-text-input::placeholder { color: #aaa; }
.chat-text-input:focus { border-color: #aaa; }

/* 画像を追加ボタン（赤アウトラインピル） */
.btn-add-image {
    background: transparent;
    border: 1.5px solid #e74c3c;
    color: #e74c3c;
    padding: 5px 14px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    white-space: nowrap;
    font-family: inherit;
    transition: background 0.15s;
    user-select: none;
    display: inline-flex;
    align-items: center;
    height: 32px;
    box-sizing: border-box;
}
.btn-add-image:hover { background: #fef2f2; }

/* ファイル入力非表示 */
.chat-input-row input[type="file"] { display: none; }

/* 送信アイコン（紙飛行機SVG） */
.btn-send-icon {
    background: #fff;
    border: 1px solid #ccc;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 6px;
    flex-shrink: 0;
    border-radius: 4px;
    transition: all 0.2s;
}
.btn-send-icon svg {
    width: 22px;
    height: 22px;
    stroke: #666;
    fill: none;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
    transition: stroke 0.2s;
}
.btn-send-icon:hover {
    background: #333;
    border-color: #333;
}
.btn-send-icon:hover svg {
    stroke: #fff;
}

/* ═══════════════════════════════════════════
   画像プレビュー
   ═══════════════════════════════════════════ */
.image-preview {
    padding: 8px 16px 0;
}
.preview-container {
    position: relative;
    display: inline-block;
    max-width: 120px;
    border-radius: 4px;
    overflow: hidden;
    border: 1px solid #e0e0e0;
    background: #f5f5f5;
}
.preview-container img {
    width: 100%;
    height: auto;
    display: block;
}
.preview-remove {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 24px;
    height: 24px;
    background: rgba(0, 0, 0, 0.7);
    color: #fff;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    font-size: 18px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}
.preview-remove:hover {
    background: rgba(0, 0, 0, 0.9);
}

/* ═══════════════════════════════════════════
   評価オーバーレイ（クリーム背景カード）
   ═══════════════════════════════════════════ */
.rating-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 2000;
    align-items: center;
    justify-content: center;
}
.rating-overlay.active { display: flex; }

.rating-card {
    background: #fefce8;
    padding: 24px 28px 20px;
    border-radius: 4px;
    width: 90%;
    max-width: 400px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.18);
}
.rating-card-title {
    font-size: 18px;
    font-weight: 700;
    color: #222;
    margin-bottom: 6px;
}
.rating-card-subtitle {
    font-size: 13px;
    color: #888;
    margin-bottom: 18px;
}
.rating-card-stars {
    display: flex;
    gap: 2px;
    margin-bottom: 18px;
}
.rating-card-stars .star {
    font-size: 38px;
    color: #ccc;
    cursor: pointer;
    user-select: none;
    transition: color 0.15s, transform 0.1s;
    line-height: 1;
}
.rating-card-stars .star:hover { transform: scale(1.1); }
.rating-card-stars .star.filled { color: #f39c12; }

/* フッター区切り＋送信するボタン */
.rating-card-footer {
    border-top: 1px solid #e5e0c0;
    padding-top: 14px;
    text-align: right;
}
.btn-rating-submit {
    background: #f87171;
    color: #fff;
    border: none;
    padding: 7px 22px;
    border-radius: 999px;
    font-size: 13px;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.2s;
}
.btn-rating-submit:hover { background: #ef4444; }
.btn-rating-submit:disabled { background: #ccc; cursor: not-allowed; }

/* ═══════════════════════════════════════════
   Lightbox
   ═══════════════════════════════════════════ */
.img-lightbox {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.82);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}
.img-lightbox.active { display: flex; }
.img-lightbox img { max-width: 90%; max-height: 90%; border-radius: 8px; }

/* ═══════════════════════════════════════════
   レスポンシブ
   ═══════════════════════════════════════════ */
.sidebar-toggle {
    display: none;
    align-items: center;
    justify-content: center;
    width: 34px; height: 34px;
    border-radius: 6px;
    border: 1px solid #dee2e6;
    background: #fff;
    cursor: pointer;
    font-size: 16px;
    color: #495057;
    flex-shrink: 0;
    transition: background 0.15s;
}
.sidebar-toggle:hover { background: #f0f4ff; }

.sidebar-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.4);
    z-index: 1040;
}
.sidebar-backdrop.active { display: block; }

/* タブレット 768‑850px */
@media (max-width: 850px) {
    .sidebar-toggle { display: flex; }

    .chat-sidebar {
        position: fixed;
        top: 0; left: -180px;
        height: 100%;
        width: 180px; min-width: 180px;
        z-index: 1050;
        box-shadow: 2px 0 16px rgba(0,0,0,0.2);
        transition: left 0.28s cubic-bezier(0.4,0,0.2,1);
    }
    .chat-sidebar.open { left: 0; }

    .chat-main-header { padding: 10px 14px; }
    .chat-header-title { font-size: 14px; }
    .btn-complete { padding: 6px 16px; font-size: 12px; }

    .chat-item-card { padding: 12px 14px; gap: 12px; }
    .chat-item-card .item-img { width: 60px; height: 60px; }
    .chat-item-card .item-card-name { font-size: 15px; }

    .chat-messages { padding: 16px 14px; }
    .chat-msg-bar { max-width: 82%; }

    .chat-input-area { padding: 10px 12px; }
}

/* PC ワイド 1400px+ */
@media (min-width: 1400px) {
    .chat-sidebar { width: 220px; min-width: 220px; }
    .chat-messages { padding: 28px 28px; }
}
</style>

<!-- サイドバーバックドロップ（タブレット用） -->
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>

<div class="chat-layout">

    <!-- ─── サイドバー: その他の取引（テキストリスト） ─── -->
    <div class="chat-sidebar" id="chatSidebar">
        <div class="chat-sidebar-header">その他の取引</div>
        <div class="chat-sidebar-list">
            @foreach ($transactionItems as $transItem)
                @if ($transItem->id !== $item->id)
                <a href="{{ route('chat.show', $transItem) }}" class="chat-sidebar-item">
                    {{ $transItem->name }}
                </a>
                @endif
            @endforeach
        </div>
    </div>

    <!-- ─── メインチャットエリア ─── -->
    <div class="chat-main">

        <!-- ヘッダー: アバター＋「〇〇」さんとの取引画面＋[取引を完了する] -->
        <div class="chat-main-header">
            <div class="chat-header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="その他の取引">☰</button>
                <div class="chat-header-avatar"></div>
                <div class="chat-header-title">「{{ ($isBuyer ? $seller : $buyer)->name }}」さんとの取引画面</div>
            </div>
            @if ($isBuyer && $item->transaction_status === 'in_transaction')
                <button type="button" class="btn-complete" onclick="openRating('buyer')">取引を完了する</button>
            @endif
        </div>

        <!-- 商品情報バー: 商品画像＋商品名＋価格 -->
        <div class="chat-item-card">
            <img src="{{ \Storage::url($item->img_url) }}"
                 class="item-img"
                 alt="{{ $item->name }}">
            <div>
                <div class="item-card-name">{{ $item->name }}</div>
                <div class="item-card-price">¥{{ number_format($item->price) }}</div>
            </div>
        </div>

        <!-- メッセージ一覧 -->
        <div class="chat-messages" id="chatMessages">
            @foreach ($messages as $message)
            <div class="chat-msg {{ $message->user_id === Auth::id() ? 'self' : '' }}">
                <!-- 名前＋アバター -->
                <div class="chat-msg-header">
                    <div class="chat-msg-avatar"></div>
                    <div class="chat-msg-name">{{ $message->user->name }}</div>
                </div>
                <!-- メッセージバー -->
                <div class="chat-msg-bar">
                    {{ $message->body }}
                    @if ($message->image)
                        <img src="{{ asset('storage/' . $message->image) }}"
                             class="msg-image"
                             alt="添付画像"
                             onclick="openLightbox(this.src)">
                    @endif
                </div>
                <!-- 編集・削除（自分のメッセージのみ） -->
                @if ($message->user_id === Auth::id())
                <div class="chat-msg-actions">
                    <a href="{{ route('chat.edit', [$item, $message]) }}">編集</a>
                    <form action="{{ route('chat.destroy', [$item, $message]) }}"
                          method="POST"
                          class="delete-form"
                          onsubmit="return confirm('このメッセージを削除しますか？')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">削除</button>
                    </form>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- 入力エリア: テキスト入力＋[画像を追加]＋送信アイコン -->
<div class="chat-input-area">
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="error-msg">{{ $error }}</div>
        @endforeach
    @endif

    <!-- 画像プレビュー -->
    <div id="imagePreview" class="image-preview" style="display: none;">
        <div class="preview-container">
            <img id="previewImg" src="" alt="プレビュー">
            <button type="button" class="preview-remove" onclick="removeImage()">×</button>
        </div>
    </div>

    <form action="{{ route('chat.store', $item) }}"
          method="POST"
          enctype="multipart/form-data"
          id="chatForm">
        @csrf
        <div class="chat-input-row">
            <input type="text"
                   name="body"
                   id="chatBody"
                   class="chat-text-input"
                   placeholder="取引メッセージを記入してください"
                   value="{{ old('body') }}"
                   autocomplete="off">
            <label class="btn-add-image" for="chatImage">画像を追加</label>
            <input type="file" name="image" id="chatImage" accept=".jpg,.jpeg,.png,.gif,.webp">
            <!-- 紙飛行機アイコン＝送信 -->
            <button type="submit" class="btn-send-icon" aria-label="送信">
                <svg viewBox="0 0 24 24">
                    <path d="M22 2L11 13"/>
                    <path d="M22 2l-7 20-4-9-9-4 20-7z"/>
                </svg>
            </button>
        </div>
    </form>
</div>

<!-- ═══════════════════════════════════════════
     購入者評価オーバーレイ（[取引を完了する]クリック時に開閉）
     ═══════════════════════════════════════════ -->
<div class="rating-overlay" id="buyerRatingOverlay">
    <div class="rating-card">
        <div class="rating-card-title">取引が完了しました。</div>
        <div class="rating-card-subtitle">今回の取引相手はどうでしたか？</div>
        <form action="{{ route('rating.buyer', $item) }}" method="POST">
            @csrf
            <div class="rating-card-stars" id="buyerStars">
                @for ($i = 1; $i <= 5; $i++)
                    <span class="star" data-value="{{ $i }}">★</span>
                @endfor
            </div>
            <input type="hidden" name="rating" id="buyerRatingValue" value="">
            <div class="rating-card-footer">
                <button type="submit" class="btn-rating-submit" id="buyerSubmitBtn" disabled>送信する</button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════════
     出品者評価オーバーレイ（ページ開放時に自動表示）
     ═══════════════════════════════════════════ -->
@if ($showRatingModal)
<div class="rating-overlay active" id="sellerRatingOverlay">
    <div class="rating-card">
        <div class="rating-card-title">取引が完了しました。</div>
        <div class="rating-card-subtitle">今回の取引相手はどうでしたか？</div>
        <form action="{{ route('rating.seller', $item) }}" method="POST">
            @csrf
            <div class="rating-card-stars" id="sellerStars">
                @for ($i = 1; $i <= 5; $i++)
                    <span class="star" data-value="{{ $i }}">★</span>
                @endfor
            </div>
            <input type="hidden" name="rating" id="sellerRatingValue" value="">
            <div class="rating-card-footer">
                <button type="submit" class="btn-rating-submit" id="sellerSubmitBtn" disabled>送信する</button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- ─── Lightbox ─── -->
<div class="img-lightbox" id="lightbox" onclick="closeLightbox()">
    <img id="lightboxImg" src="" alt="画像拡大">
</div>

<script>
// ─── 評価オーバーレイ開閉 ───
function openRating(type) {
    document.getElementById(type + 'RatingOverlay').classList.add('active');
}

// ─── スター評価 ───
function setupStarRating(containerId, hiddenId, submitBtnId) {
    var container   = document.getElementById(containerId);
    if (!container) return;
    var stars       = container.querySelectorAll('.star');
    var hiddenInput = document.getElementById(hiddenId);
    var submitBtn   = document.getElementById(submitBtnId);
    var selected    = 0;

    function highlight(value) {
        stars.forEach(function (s) {
            s.classList.toggle('filled', parseInt(s.dataset.value) <= value);
        });
    }

    stars.forEach(function (star) {
        star.addEventListener('mouseover', function () { highlight(parseInt(this.dataset.value)); });
        star.addEventListener('mouseout',  function () { highlight(selected); });
        star.addEventListener('click',     function () {
            selected            = parseInt(this.dataset.value);
            hiddenInput.value   = selected;
            highlight(selected);
            if (submitBtn) submitBtn.disabled = false;
        });
    });
}

setupStarRating('buyerStars',  'buyerRatingValue',  'buyerSubmitBtn');
setupStarRating('sellerStars', 'sellerRatingValue', 'sellerSubmitBtn');

// ─── メッセージ末尾スクロール ───
(function () {
    var el = document.getElementById('chatMessages');
    if (el) el.scrollTop = el.scrollHeight;
})();

// ─── FN009: 入力情報保持 ───
(function () {
    var DRAFT_KEY = 'chat_draft_{{ $item->id }}';
    var chatBody  = document.getElementById('chatBody');

    @if (session('message_sent'))
        localStorage.removeItem(DRAFT_KEY);
    @else
        if (!chatBody.value) {
            var draft = localStorage.getItem(DRAFT_KEY);
            if (draft) chatBody.value = draft;
        }
    @endif

    chatBody.addEventListener('input', function () {
        localStorage.setItem(DRAFT_KEY, this.value);
    });
})();

// ─── Lightbox ───
function openLightbox(src) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightbox').classList.add('active');
}
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('active');
}

// ─── レスポンシブ: サイドバートグル ───
function toggleSidebar() {
    var sidebar  = document.getElementById('chatSidebar');
    var backdrop = document.getElementById('sidebarBackdrop');
    if (sidebar.classList.contains('open')) {
        closeSidebar();
    } else {
        sidebar.classList.add('open');
        backdrop.classList.add('active');
    }
}
function closeSidebar() {
    document.getElementById('chatSidebar').classList.remove('open');
    document.getElementById('sidebarBackdrop').classList.remove('active');
}

// ─── 画像プレビュー ───
document.getElementById('chatImage').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

function removeImage() {
    document.getElementById('chatImage').value = '';
    document.getElementById('imagePreview').style.display = 'none';
}
</script>
</body>
</html>
