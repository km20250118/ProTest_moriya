<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\User;
use App\Models\SoldItem;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Payment;

class PurchaseController extends Controller
{
  public function index($item_id, Request $request)
  {
    $item = Item::find($item_id);
    Auth::user()->refresh();
    $user = Auth::user();
    return view('purchase', compact('item', 'user'));
  }

  public function purchase(Request $request, $item_id)
  {
    $item = Item::findOrFail($item_id);
    $user = auth()->user();

    $paymentMethod = $request->input('payment_method');

    if ($paymentMethod === 'card') {
      Stripe::setApiKey(config('stripe.secret_key'));

      try {
        $paymentIntent = PaymentIntent::create([
          'amount' => $item->price,
          'currency' => 'jpy',
          'automatic_payment_methods' => [
            'enabled' => true,
          ],
          'metadata' => [
            'item_id' => $item->id,
            'user_id' => $user->id,
          ],
        ]);

        session([
          'payment_intent_id' => $paymentIntent->id,
          'item_id' => $item_id,
        ]);

        return view('purchase.stripe', [
          'item' => $item,
          'clientSecret' => $paymentIntent->client_secret,
        ]);
      } catch (\Exception $e) {
        return back()->with('error', '決済の準備に失敗しました: ' . $e->getMessage());
      }
    } else {
      // ─── コンビニ払いの処理 ───
      // 購入履歴を保存
      SoldItem::create([
        'item_id' => $item->id,
        'user_id' => $user->id,
        'sending_postcode' => $user->postal_code,
        'sending_address' => $user->address,
        'sending_building' => $user->building,
      ]);

      // チャット機能: 取引ステータスを設定
      $item->update(['transaction_status' => 'in_transaction']);

      return redirect()->route('purchase.index', ['item_id' => $item_id])
        ->with('success', '購入処理を完了しました');
    }
  }

  public function address($item_id)
  {
    $item = Item::find($item_id);
    $user = Auth::user();
    $address = $user->profile;
    return view('address', compact('item', 'address', 'item_id'));
  }

  public function updateAddress(Request $request, $item_id)
  {
    $user = Auth::user();
    $user->postal_code = $request->postal_code;
    $user->address = $request->address;
    $user->building = $request->building;
    $user->save();
    return redirect("/purchase/{$item_id}");
  }

  public function success(Request $request, $item_id)
  {
    $paymentIntentId = $request->query('payment_intent');
    $item = Item::find($item_id);
    $user = auth()->user();

    if ($paymentIntentId) {
      Stripe::setApiKey(config('stripe.secret_key'));

      try {
        $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

        // 決済情報を保存
        Payment::create([
          'user_id' => $user->id,
          'stripe_payment_id' => $paymentIntent->id,
          'amount' => $paymentIntent->amount,
          'currency' => $paymentIntent->currency,
          'status' => $paymentIntent->status,
          'description' => '商品購入: ' . $item->name,
        ]);

        // 購入履歴を保存（sold_itemsテーブル）
        SoldItem::create([
          'item_id' => $item->id,
          'user_id' => $user->id,
          'sending_postcode' => $user->postal_code,
          'sending_address' => $user->address,
          'sending_building' => $user->building,
        ]);

        // ─── チャット機能: 取引ステータスを設定 ───
        $item->update(['transaction_status' => 'in_transaction']);

        return view('purchase.success', compact('item', 'paymentIntent'));
      } catch (\Exception $e) {
        return redirect()->route('purchase.index', $item_id)
          ->with('error', '決済の確認に失敗しました: ' . $e->getMessage());
      }
    }

    return view('purchase.success', compact('item'));
  }
}
