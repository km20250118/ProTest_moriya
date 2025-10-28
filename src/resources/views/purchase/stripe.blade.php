@extends('layouts.default')
@section('title','クレジットカード決済')
@section('css')
<style>
    .stripe-container {
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
    }
    .item-summary {
        background: #f9f9f9;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 30px;
        display: flex;
        gap: 20px;
        align-items: center;
    }
    .item-summary img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
    }
    .item-info h3 {
        font-size: 24px;
        margin-bottom: 10px;
    }
    .item-info .price {
        font-size: 28px;
        color: #e74c3c;
        font-weight: bold;
    }
    .payment-section {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .payment-section h2 {
        margin-bottom: 20px;
        font-size: 20px;
    }
    #card-element {
        border: 2px solid #ddd;
        padding: 15px;
        border-radius: 4px;
        background: white;
        margin: 20px 0;
    }
    .pay-button {
        width: 100%;
        padding: 18px;
        background: #e74c3c;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 18px;
        cursor: pointer;
        margin-top: 20px;
        font-weight: bold;
    }
    .pay-button:hover {
        background: #c0392b;
    }
    .pay-button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .error-message {
        color: #e74c3c;
        margin-top: 10px;
        font-size: 14px;
    }
    .back-link {
        display: inline-block;
        margin-bottom: 20px;
        color: #3498db;
        text-decoration: none;
    }
    .back-link:hover {
        text-decoration: underline;
    }
</style>
@endsection

@section('content')
@include('components.header')

<div class="stripe-container">
    <a href="/purchase/{{ $item->id }}" class="back-link">← 購入画面に戻る</a>
    
    <h1>クレジットカード決済</h1>
    
    <div class="item-summary">
        <img src="{{ \Storage::url($item->img_url) }}" alt="{{ $item->name }}">
        <div class="item-info">
            <h3>{{ $item->name }}</h3>
            <p class="price">¥{{ number_format($item->price) }}</p>
        </div>
    </div>

    <div class="payment-section">
        <h2>カード情報を入力してください</h2>
        
        <form id="payment-form">
            @csrf
            <div id="card-element"></div>
            
            <button type="submit" id="submit-button" class="pay-button">
                ¥{{ number_format($item->price) }} を支払う
            </button>
            
            <div id="error-message" class="error-message"></div>
        </form>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ config("stripe.public_key") }}');
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

    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const errorMessage = document.getElementById('error-message');

    cardElement.on('change', function(event) {
        if (event.error) {
            errorMessage.textContent = event.error.message;
        } else {
            errorMessage.textContent = '';
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        submitButton.disabled = true;
        submitButton.textContent = '処理中...';
        errorMessage.textContent = '';

        try {
            const { error, paymentIntent } = await stripe.confirmCardPayment(
                '{{ $clientSecret }}',
                {
                    payment_method: {
                        card: cardElement,
                    }
                }
            );

            if (error) {
                errorMessage.textContent = error.message;
                submitButton.disabled = false;
                submitButton.textContent = '¥{{ number_format($item->price) }} を支払う';
            } else if (paymentIntent.status === 'succeeded') {
                window.location.href = '/purchase/{{ $item->id }}/success?payment_intent=' + paymentIntent.id;
            }
        } catch (err) {
            console.error(err);
            errorMessage.textContent = 'エラーが発生しました';
            submitButton.disabled = false;
            submitButton.textContent = '¥{{ number_format($item->price) }} を支払う';
        }
    });
</script>
@endsection