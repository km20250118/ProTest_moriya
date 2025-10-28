<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>決済フォーム</title>
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 500px; 
            margin: 50px auto; 
            padding: 20px; 
        }
        #payment-form { 
            margin-top: 20px; 
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        #card-element { 
            border: 1px solid #ccc; 
            padding: 12px; 
            border-radius: 4px;
            background: white;
            min-height: 40px;
        }
        button { 
            margin-top: 20px; 
            padding: 12px 24px; 
            background: #5469d4; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        button:hover {
            background: #4054b2;
        }
        button:disabled { 
            opacity: 0.5; 
            cursor: not-allowed; 
        }
        #error-message { 
            color: red; 
            margin-top: 10px; 
            min-height: 20px;
        }
        #success-message {
            color: green;
            margin-top: 10px;
        }
        .loading {
            display: none;
            margin-top: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>決済フォーム</h1>
    
    <form id="payment-form">
        <div class="form-group">
            <label for="amount">金額（円）:</label>
            <input type="number" id="amount" name="amount" value="1000" min="100" required>
        </div>
        
        <div class="form-group">
            <label for="card-element">カード情報:</label>
            <div id="card-element"></div>
        </div>
        
        <button type="submit" id="submit-button">支払う</button>
        
        <div class="loading" id="loading">処理中...</div>
        <div id="error-message"></div>
        <div id="success-message"></div>
    </form>

    <script>
        console.log('Script loaded');
        
        // Stripeキーの確認
        const stripeKey = '{{ config("stripe.public_key") }}';
        console.log('Stripe Key:', stripeKey ? 'キーが設定されています' : 'キーが設定されていません');
        
        if (!stripeKey || stripeKey.indexOf('pk_') !== 0) {
            document.getElementById('error-message').textContent = 'Stripe公開キーが正しく設定されていません';
            document.getElementById('submit-button').disabled = true;
        } else {
            try {
                const stripe = Stripe(stripeKey);
                console.log('Stripe initialized');
                
                const elements = stripe.elements();
                const cardElement = elements.create('card', {
                    style: {
                        base: {
                            fontSize: '16px',
                            color: '#32325d',
                            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                            '::placeholder': {
                                color: '#aab7c4'
                            }
                        },
                        invalid: {
                            color: '#fa755a',
                            iconColor: '#fa755a'
                        }
                    }
                });
                
                cardElement.mount('#card-element');
                console.log('Card element mounted');
                
                // カード要素のイベント
                cardElement.on('ready', function() {
                    console.log('Card element is ready');
                });
                
                cardElement.on('change', function(event) {
                    console.log('Card element changed:', event);
                    const errorMessage = document.getElementById('error-message');
                    if (event.error) {
                        errorMessage.textContent = event.error.message;
                    } else {
                        errorMessage.textContent = '';
                    }
                });

                const form = document.getElementById('payment-form');
                const submitButton = document.getElementById('submit-button');
                const errorMessage = document.getElementById('error-message');
                const loading = document.getElementById('loading');

                form.addEventListener('submit', async (event) => {
                    event.preventDefault();
                    console.log('Form submitted');
                    
                    submitButton.disabled = true;
                    loading.style.display = 'block';
                    errorMessage.textContent = '';

                    const amount = document.getElementById('amount').value;

                    try {
                        console.log('Creating payment intent for amount:', amount);
                        
                        // Payment Intentの作成
                        const response = await fetch('/payment/create-intent', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ amount: amount })
                        });

                        if (!response.ok) {
                            throw new Error('Payment intent creation failed');
                        }

                        const data = await response.json();
                        console.log('Payment intent created:', data);

                        // 決済の確認
                        const { error, paymentIntent } = await stripe.confirmCardPayment(data.clientSecret, {
                            payment_method: {
                                card: cardElement,
                            }
                        });

                        if (error) {
                            console.error('Payment error:', error);
                            errorMessage.textContent = error.message;
                            submitButton.disabled = false;
                            loading.style.display = 'none';
                        } else if (paymentIntent.status === 'succeeded') {
                            console.log('Payment succeeded:', paymentIntent);
                            window.location.href = `/payment/success?payment_intent=${paymentIntent.id}`;
                        }
                    } catch (err) {
                        console.error('Error:', err);
                        errorMessage.textContent = 'エラーが発生しました: ' + err.message;
                        submitButton.disabled = false;
                        loading.style.display = 'none';
                    }
                });
                
            } catch (error) {
                console.error('Stripe initialization error:', error);
                document.getElementById('error-message').textContent = 'Stripeの初期化に失敗しました';
            }
        }
    </script>
</body>
</html>