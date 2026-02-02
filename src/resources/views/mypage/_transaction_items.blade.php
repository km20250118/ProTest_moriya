{{--
    取引中商品表示パーシャル（マイページタブコンテンツ）
--}}

<style>
/* 通知ドット */
.item {
    position: relative;
}
.transaction-unread-dot {
    position: absolute;
    top: 6px;
    left: 6px;
    width: 14px;
    height: 14px;
    background: #e74c3c;
    border-radius: 50%;
    border: 2.5px solid #fff;
    z-index: 10;
    box-shadow: 0 1px 3px rgba(0,0,0,0.25);
}
</style>

<div class="items">
    @foreach ($transactionItems as $transItem)
    <div class="item">
        <a href="{{ route('chat.show', $transItem) }}">
            @if ($transItem->unread_count > 0)
                <span class="transaction-unread-dot"></span>
            @endif
            
            <div class="item__img--container">
                <img src="{{ \Storage::url($transItem->img_url) }}" class="item__img" alt="商品画像">
            </div>
            <p class="item__name">{{ $transItem->name }}</p>
        </a>
    </div>
    @endforeach
</div>

@if ($transactionItems->isEmpty())
    <p class="text-muted text-center" style="font-size: 14px; padding: 40px 0;">
        取引中の商品はありません
    </p>
@endif