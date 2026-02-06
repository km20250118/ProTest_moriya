@if($transactionItems->count() > 0)
    <div class="items">
        @foreach($transactionItems as $item)
            <a href="{{ route('chat.show', $item->id) }}" class="item">
                <div class="item__img--container {{ $item->transaction_status === 'completed' ? 'sold' : '' }}">
                    @php
                        // 未読メッセージ数を計算
                        $isBuyer = $item->soldItem && $item->soldItem->user_id === auth()->id();
                        $unreadCount = $item->chatMessages()
                            ->where('user_id', '!=', auth()->id())
                            ->where($isBuyer ? 'read_by_buyer' : 'read_by_seller', false)
                            ->count();
                    @endphp
                    
                    @if($unreadCount > 0)
                        <span class="item-unread-badge">{{ $unreadCount }}</span>
                    @endif
                    
                    @if($item->img_url)
                        <img src="{{ asset('storage/' . $item->img_url) }}" 
                             alt="{{ $item->name }}" 
                             class="item__img">
                    @else
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #999; font-size: 14px;">
                            画像なし
                        </div>
                    @endif
                </div>
                <div class="item__name">{{ $item->name }}</div>
                <div class="item__text">¥{{ number_format($item->price) }}</div>
            </a>
        @endforeach
    </div>
@else
    <div style="padding: 60px 20px; text-align: center; color: #999;">
        <p>取引中の商品はありません</p>
    </div>
@endif

<style>
/* 商品画像コンテナ */
.item__img--container {
    position: relative !important;
}

/* 未読バッジ */
.item-unread-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #ff0000;
    color: #fff;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 700;
    z-index: 10;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    line-height: 1;
}

@media (max-width: 768px) {
    .item-unread-badge {
        width: 24px;
        height: 24px;
        font-size: 12px;
        top: 8px;
        left: 8px;
    }
}
</style>
