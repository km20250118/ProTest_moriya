<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>取引画面 - coachtech フリマ</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans JP', sans-serif;
            background-color: #f5f5f5;
            overflow-x: hidden;
        }

        /* ========== ヘッダー ========== */
        .simple-header {
            background-color: #000;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .simple-header__logo {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .simple-header__logo-image {
            height: 28px;
            width: auto;
        }

        /* ========== メインコンテナ ========== */
        .chat-container {
            display: flex;
            height: calc(100vh - 52px);
            background: #fff9e6;
        }

        /* ========== サイドバー ========== */
        .sidebar {
            width: 200px;
            background: #6b6b6b;
            color: #fff;
            overflow-y: auto;
            flex-shrink: 0;
            padding: 0;
        }

        .sidebar__header {
            padding: 20px 16px;
            background: #6b6b6b;
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            border-bottom: 1px solid #7b7b7b;
        }

        .sidebar__list {
            list-style: none;
            padding: 12px 10px;
        }

        .sidebar__item {
            margin-bottom: 16px;
        }

        .sidebar__link {
            display: block;
            padding: 12px 8px;
            text-align: center;
            text-decoration: none;
            color: #333;
            background: #e8e8e8;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sidebar__link:hover {
            background: #d8d8d8;
        }

        .sidebar__link.active {
            background: #e8e8e8;
            font-weight: 600;
        }

        .unread-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff385c;
            color: #fff;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 5px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
        }

        /* ========== メインコンテンツ ========== */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #fff9e6;
            overflow: hidden;
        }

        /* ========== チャットヘッダー ========== */
        .chat-header {
            padding: 16px 24px;
            background: #f9f9f9;
            border-bottom: 1px solid #999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .chat-header__user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chat-header__avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 20px;
        }

        .chat-header__title {
            font-size: 16px;
            font-weight: 500;
            color: #333;
        }

        .complete-button {
            background: #ff8fa3;
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .complete-button:hover {
            background: #ff7a91;
        }

        /* ========== 商品情報カード ========== */
        .product-card {
            display: flex;
            gap: 20px;
            padding: 20px 24px;
            background: #fff;
            border-bottom: 1px solid #999;
            border-top: 1px solid #999;
        }

        .product-card__image {
            width: 120px;
            height: 120px;
            border-radius: 4px;
            background: #ffffff !important;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 14px;
            overflow: hidden;
        }

        .product-card__image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-card__info {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 12px;
        }

        .product-card__name {
            font-size: 22px;
            font-weight: 700;
            color: #333;
        }

        .product-card__price {
            font-size: 16px;
            color: #666;
        }

        /* ========== メッセージエリア ========== */
        .messages-area {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            background: #fff;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .message {
            display: flex;
            gap: 12px;
            max-width: 60%;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .message.sent {
            margin-left: auto;
            flex-direction: row-reverse;
        }

        .message__avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 16px;
            flex-shrink: 0;
        }

        .message__content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .message__sender {
            font-size: 13px;
            font-weight: 500;
            color: #666;
        }

        .message.sent .message__sender {
            text-align: right;
        }

        .message__bubble {
            background: #e0e0e0;
            padding: 12px 16px;
            border-radius: 8px;
            word-wrap: break-word;
        }

        .message.sent .message__bubble {
            background: #e0e0e0;
            color: #333;
        }

        .message__text {
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
        }

        .message__image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-top: 8px;
            cursor: pointer;
        }

        .message__actions {
            display: flex;
            gap: 12px;
            margin-top: 4px;
            font-size: 12px;
        }

        .message.sent .message__actions {
            justify-content: flex-end;
        }

        .message__action-btn {
            color: #999;
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            text-decoration: underline;
            transition: color 0.2s ease;
        }

        .message__action-btn:hover {
            color: #666;
        }

        /* ========== 入力エリア ========== */
        .input-area {
            padding: 16px 24px;
            background: #fff;
            border-top: 1px solid #999;
            flex-shrink: 0;
        }

        .input-area__form {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .input-area__textarea {
            flex: 1;
            height: 44px;
            padding: 12px 16px;
            border: 2px solid #333 !important;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
            resize: none;
            overflow: hidden;
            transition: border-color 0.2s ease;
        }

        .input-area__textarea::placeholder {
            color: #666;
        }

        .input-area__textarea:focus {
            outline: none;
            border-color: #999 !important;
        }

        .input-area__image-label {
            display: inline-flex;
            align-items: center;
            padding: 12px 20px;
            background: #fff;
            border: 2px solid #e03131;
            border-radius: 8px;
            font-size: 15px;
            color: #e03131;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .input-area__image-label:hover {
            background: #fff5f5;
        }

        .input-area__image-input {
            display: none;
        }

        .input-area__submit {
            background: none;
            border: none;
            padding: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .input-area__submit svg {
            width: 36px;
            height: 36px;
            fill: none;
            stroke: #999;
            stroke-width: 1.5;
            transition: stroke 0.2s ease;
        }

        .input-area__submit:hover svg {
            stroke: #666;
        }

        .input-area__preview {
            margin-top: 12px;
            position: relative;
            display: inline-block;
        }

        .input-area__preview-image {
            max-width: 150px;
            max-height: 150px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .input-area__preview-remove {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 24px;
            height: 24px;
            background: #ff385c;
            color: #fff;
            border: 2px solid #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 16px;
            line-height: 1;
        }

        .error-message {
            color: #ff385c;
            font-size: 13px;
            margin-top: 4px;
        }

        /* ========== モーダル ========== */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal__content {
            background: #fff9e6;
            padding: 40px 32px;
            border-radius: 12px;
            border: 2px solid #333;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal__header {
            text-align: left;
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 2px solid #999;
        }

        .modal__title {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .modal__subtitle {
            font-size: 17px;
            color: #999;
            margin-bottom: 24px;
            padding-top: 24px;
        }

        .modal__form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .modal__field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .modal__label {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        .star-rating {
            display: flex;
            gap: 12px;
            justify-content: center;
            padding-bottom: 24px;
            border-bottom: 2px solid #999;
        }

        .star-rating__input {
            display: none;
        }

        .star-rating__label {
            font-size: 80px;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .star-rating__label:hover,
        .star-rating__label.active {
            color: #ffd700;
        }

        .modal__textarea {
            width: 100%;
            min-height: 100px;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            resize: vertical;
        }

        .modal__textarea:focus {
            outline: none;
            border-color: #ff385c;
        }

        .modal__actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 24px;
        }

        .modal__button {
            padding: 12px 40px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .modal__button--secondary {
            background: #f5f5f5;
            color: #666;
        }

        .modal__button--secondary:hover {
            background: #e0e0e0;
        }

        .modal__button--primary {
            background: #ff8fa3;
            font-size: 17px;
            color: #fff;
        }

        .modal__button--primary:hover {
            background: #ff7a91;
        }

        /* ========== レスポンシブ対応 ========== */
        @media (max-width: 850px) {
            .chat-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                max-height: 200px;
                border-right: none;
                border-bottom: 1px solid #7b7b7b;
            }

            .sidebar__list {
                display: flex;
                overflow-x: auto;
                padding: 10px;
                gap: 10px;
            }

            .sidebar__item {
                margin-bottom: 0;
                min-width: 140px;
            }

            .message {
                max-width: 75%;
            }
        }

        @media (max-width: 768px) {
            .simple-header {
                padding: 10px 16px;
            }

            .simple-header__logo-image {
                height: 24px;
            }

            .chat-header {
                padding: 12px 16px;
            }

            .chat-header__avatar {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }

            .complete-button {
                padding: 8px 16px;
                font-size: 13px;
            }

            .product-card {
                padding: 16px;
                gap: 12px;
            }

            .product-card__image {
                width: 80px;
                height: 80px;
            }

            .product-card__name {
                font-size: 16px;
            }

            .product-card__price {
                font-size: 14px;
            }

            .messages-area {
                padding: 16px;
            }

            .message {
                max-width: 85%;
            }

            .input-area {
                padding: 12px 16px;
            }

            .input-area__form {
                flex-wrap: wrap;
            }

            .input-area__textarea {
                width: 100%;
            }

            .modal__content {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <!-- シンプルヘッダー -->
    <header class="simple-header">
        <a href="{{ url('/') }}" class="simple-header__logo">
            <img src="{{ asset('img/logo.png') }}" alt="COACHTECH" class="simple-header__logo-image">
        </a>
    </header>

    <!-- メインコンテナ -->
    <div class="chat-container">
        <!-- サイドバー -->
        <aside class="sidebar">
            <div class="sidebar__header">その他の取引</div>
            <ul class="sidebar__list">
                @forelse($transactionItems as $transactionItem)
                    <li class="sidebar__item">
                        <a href="{{ route('chat.show', $transactionItem->id) }}" 
                           class="sidebar__link {{ $transactionItem->id == $item->id ? 'active' : '' }}">
                            {{ $transactionItem->name }}
                            
                            @php
                                $unreadCount = $transactionItem->chatMessages()
                                    ->where('user_id', '!=', auth()->id())
                                    ->where($isBuyer ? 'read_by_buyer' : 'read_by_seller', false)
                                    ->count();
                            @endphp
                            
                            @if($unreadCount > 0)
                                <span class="unread-badge">{{ $unreadCount }}</span>
                            @endif
                        </a>
                    </li>
                @empty
                    <li style="padding: 20px; text-align: center; color: #ccc; font-size: 12px;">
                        取引中の商品はありません
                    </li>
                @endforelse
            </ul>
        </aside>

        <!-- メインコンテンツ -->
        <main class="main-content">
            <!-- チャットヘッダー -->
            <div class="chat-header">
                <div class="chat-header__user">
                    <div class="chat-header__avatar">
                        {{ mb_substr($isBuyer ? $seller->name : $buyer->name, 0, 1) }}
                    </div>
                    <h2 class="chat-header__title">「{{ $isBuyer ? $seller->name : $buyer->name }}」さんとの取引画面</h2>
                </div>
                
                @if($isBuyer && $item->transaction_status !== 'completed')
                    <button type="button" class="complete-button" onclick="openBuyerRatingModal()">
                        取引を完了する
                    </button>
                @endif
            </div>

            <!-- 商品情報カード -->
            <div class="product-card">
                <div class="product-card__image">
                    @if($item->img_url)
                        <img src="{{ asset('storage/' . $item->img_url) }}" 
                             alt="{{ $item->name }}">
                    @else
                        商品画像
                    @endif
                </div>
                <div class="product-card__info">
                    <div class="product-card__name">{{ $item->name }}</div>
                    <div class="product-card__price">¥{{ number_format($item->price) }}</div>
                </div>
            </div>

            <!-- メッセージエリア -->
            <div class="messages-area" id="messagesArea">
                @forelse($messages as $message)
                    <div class="message {{ $message->user_id == auth()->id() ? 'sent' : 'received' }}">
                        <div class="message__avatar">
                            {{ mb_substr($message->user->name, 0, 1) }}
                        </div>
                        <div class="message__content">
                            <div class="message__sender">{{ $message->user->name }}</div>
                            <div class="message__bubble">
                                @if($message->body)
                                    <p class="message__text">{{ $message->body }}</p>
                                @endif
                                @if($message->image)
                                    <img src="{{ asset('storage/' . $message->image) }}" 
                                         alt="添付画像" 
                                         class="message__image">
                                @endif
                            </div>
                            @if($message->user_id == auth()->id())
                                <div class="message__actions">
                                    <a href="{{ route('chat.edit', [$item->id, $message->id]) }}" class="message__action-btn">編集</a>
                                    <form action="{{ route('chat.destroy', [$item->id, $message->id]) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="message__action-btn" 
                                                onclick="return confirm('このメッセージを削除しますか?')">削除</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; color: #999; padding: 40px;">
                        メッセージはまだありません
                    </div>
                @endforelse
            </div>

            <!-- 入力エリア -->
            <div class="input-area">
                <form action="{{ route('chat.store', $item->id) }}" method="POST" enctype="multipart/form-data" class="input-area__form">
                    @csrf
                    <textarea 
                        name="body" 
                        class="input-area__textarea" 
                        placeholder="取引メッセージを記入してください"
                        id="messageBody">{{ old('body') }}</textarea>
                    
                    <label for="imageInput" class="input-area__image-label">
                        画像を追加
                    </label>
                    <input 
                        type="file" 
                        name="image" 
                        id="imageInput" 
                        class="input-area__image-input"
                        accept="image/*"
                        onchange="previewImage(event)">
                    
                    <button type="submit" class="input-area__submit">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                    </button>
                </form>
                
                @error('body')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                @error('image')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                
                <div id="imagePreview" class="input-area__preview" style="display: none;">
                    <img id="previewImg" class="input-area__preview-image" src="" alt="プレビュー">
                    <span class="input-area__preview-remove" onclick="removeImage()">×</span>
                </div>
            </div>
        </main>
    </div>

    <!-- 購入者評価モーダル -->
    <div id="buyerRatingModal" class="modal">
        <div class="modal__content">
            <div class="modal__header">
                <h3 class="modal__title">取引が完了しました。</h3>
            </div>
            <p class="modal__subtitle">今回の取引相手はどうでしたか？</p>
            <form action="{{ route('rating.buyer', $item->id) }}" method="POST" class="modal__form">
                @csrf
                <div class="modal__field">
                    <div class="star-rating">
                        @for($i = 1; $i <= 5; $i++)
                            <input type="radio" name="rating" value="{{ $i }}" id="buyer-star{{ $i }}" class="star-rating__input" required>
                            <label for="buyer-star{{ $i }}" class="star-rating__label" onclick="setRating('buyer', {{ $i }})">★</label>
                        @endfor
                    </div>
                </div>
                <input type="hidden" name="comment" value="評価">
                <div class="modal__actions">
                    <button type="submit" class="modal__button modal__button--primary">
                        送信する
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($showRatingModal ?? false)
    <div id="sellerRatingModal" class="modal active">
        <div class="modal__content">
            <div class="modal__header">
                <h3 class="modal__title">取引が完了しました。</h3>
            </div>
            <p class="modal__subtitle">今回の取引相手はどうでしたか？</p>
            <form action="{{ route('rating.seller', $item->id) }}" method="POST" class="modal__form">
                @csrf
                <div class="modal__field">
                    <div class="star-rating">
                        @for($i = 1; $i <= 5; $i++)
                            <input type="radio" name="rating" value="{{ $i }}" id="seller-star{{ $i }}" class="star-rating__input" required>
                            <label for="seller-star{{ $i }}" class="star-rating__label" onclick="setRating('seller', {{ $i }})">★</label>
                        @endfor
                    </div>
                </div>
                <input type="hidden" name="comment" value="評価">
                <div class="modal__actions">
                    <button type="submit" class="modal__button modal__button--primary">
                        送信する
                    </button>
                </div>
            </form>
        </div>
    @endif

 <script>
        // メッセージエリアを最下部にスクロール
        const messagesArea = document.getElementById('messagesArea');
        if (messagesArea) {
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }

        // 画像プレビュー
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'inline-block';
                }
                reader.readAsDataURL(file);
            }
        }

        // 画像削除
        function removeImage() {
            document.getElementById('imageInput').value = '';
            document.getElementById('imagePreview').style.display = 'none';
        }

        // 購入者評価モーダル
        function openBuyerRatingModal() {
            document.getElementById('buyerRatingModal').classList.add('active');
        }

        function closeBuyerRatingModal() {
            document.getElementById('buyerRatingModal').classList.remove('active');
        }

        // 星評価

        // 星評価
        function setRating(type, rating) {
            const prefix = type === 'buyer' ? 'buyer' : 'seller';
            const modalId = type === 'buyer' ? 'buyerRatingModal' : 'sellerRatingModal';
            
            // 選択した星のラジオボタンをチェック
            document.getElementById(`${prefix}-star${rating}`).checked = true;
            
            // すべての星のラベルを取得
            const stars = document.querySelectorAll(`#${modalId} .star-rating__label`);
            
            // クリックした星までをアクティブに
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
        }

        // LocalStorageでメッセージを保存
        const textarea = document.getElementById('messageBody');
        if (textarea) {
            const storageKey = 'chat_message_{{ $item->id }}';
            
            const savedMessage = localStorage.getItem(storageKey);
            if (savedMessage && !textarea.value) {
                textarea.value = savedMessage;
            }
            
            textarea.addEventListener('input', function() {
                localStorage.setItem(storageKey, this.value);
            });
            
            textarea.closest('form').addEventListener('submit', function() {
                localStorage.removeItem(storageKey);
            });
        }
    </script>
</body>
</html>
