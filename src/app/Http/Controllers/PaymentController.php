<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret_key'));
    }

    // 決済フォーム表示
    public function showPaymentForm()
    {
        return view('payment.form');
    }

    // Payment Intentの作成
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:100',
        ]);

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount,
                'currency' => 'jpy',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 決済成功後の処理
    public function success(Request $request)
    {
        $request->validate([
            'payment_intent' => 'required|string',
        ]);

        try {
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent);

            // DBに保存
            Payment::create([
                'user_id' => auth()->id(),
                'stripe_payment_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
                'status' => $paymentIntent->status,
                'description' => '商品購入',
            ]);

            return view('payment.success', ['payment' => $paymentIntent]);
        } catch (\Exception $e) {
            return redirect()->route('payment.form')->with('error', $e->getMessage());
        }
    }

    // Webhook処理
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Webhook error'], 400);
        }

        // イベント処理
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                // 決済成功時の処理
                \Log::info('Payment succeeded: ' . $paymentIntent->id);
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                // 決済失敗時の処理
                \Log::error('Payment failed: ' . $paymentIntent->id);
                break;
        }

        return response()->json(['status' => 'success']);
    }
}
