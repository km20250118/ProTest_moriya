<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Hiragino Kaku Gothic Pro', 'MS Gothic', Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f6f8;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: #fff;
            padding: 28px 24px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }
        .header p {
            margin: 6px 0 0;
            font-size: 13px;
            opacity: 0.85;
        }
        .body {
            padding: 32px 28px;
        }
        .greeting {
            font-size: 15px;
            margin-bottom: 20px;
        }
        .item-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 18px 20px;
            margin: 20px 0;
        }
        .item-card .label {
            font-size: 11px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .item-card .value {
            font-size: 15px;
            font-weight: 600;
            color: #333;
            margin-bottom: 12px;
        }
        .item-card .value:last-child {
            margin-bottom: 0;
        }
        .item-card .price {
            color: #e74c3c;
        }
        .note {
            font-size: 13px;
            color: #666;
            line-height: 1.6;
            margin-top: 24px;
        }
        .footer {
            background: #f8f9fa;
            border-top: 1px solid #eee;
            padding: 20px 28px;
            text-align: center;
        }
        .footer p {
            font-size: 11px;
            color: #999;
            margin: 0;
            line-height: 1.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✓ 取引完了通知</h1>
            <p>フリマアプリ</p>
        </div>
        <div class="body">
            <p class="greeting">{{ $seller->name }} さん、こんにちは。</p>
            <p>以下の商品の取引が完了しました。</p>

            <div class="item-card">
                <div class="label">商品名</div>
                <div class="value">{{ $item->name }}</div>
                <div class="label">価格</div>
                <div class="value price">¥{{ number_format($item->price) }}</div>
            </div>

            <p class="note">
                取引が完了したため、アプリ内で購入者の評価を行うことができます。<br>
                ご不明な点があればアプリ内のチャットでご確認ください。
            </p>
        </div>
        <div class="footer">
            <p>このメールは自動送信されたものです。返信には対応しておりません。<br>
            ご質問はアプリ内でお問い合わせください。</p>
        </div>
    </div>
</body>
</html>
