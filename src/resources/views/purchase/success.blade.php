<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>購入完了</title>
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
</head>
<body>
    @component('components.header')
    @endcomponent

    <div class="success-container">
        <div class="success-card">
            <h2 class="success-title">購入が完了しました</h2>
            <p class="success-message">ご購入ありがとうございました。</p>
            
            <div class="item-info">
                <h3>購入商品</h3>
                <p class="item-name">{{ $item->name }}</p>
                <p class="item-price">¥{{ number_format($item->price) }}</p>
            </div>
            
            <div class="success-buttons">
                <a href="/" class="btn btn-primary">トップページに戻る</a>
            </div>
        </div>
    </div>
</body>
</html>
