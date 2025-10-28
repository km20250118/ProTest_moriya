<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>決済完了</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: 50px auto; padding: 20px; text-align: center; }
        .success { color: green; font-size: 24px; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>決済が完了しました</h1>
    <div class="success">✓</div>
    <p>決済ID: {{ $payment->id }}</p>
    <p>金額: ¥{{ number_format($payment->amount) }}</p>
    <a href="/">トップページへ戻る</a>
</body>
</html>