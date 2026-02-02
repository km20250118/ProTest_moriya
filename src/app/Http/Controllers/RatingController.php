<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RatingRequest;
use App\Mail\TransactionCompleteMail;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class RatingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 購入者の評価投稿（出品者を評価）＋取引完了
     * FN012: 取引完了ボタン → モーダル → 評価送信
     * FN016: 出品者へのメール通知
     * FN014: 評価送信後は商品一覧画面へ遷移
     */
    public function storeBuyer(Item $item, RatingRequest $request)
    {
        $user   = Auth::user();
        $buyer  = $item->soldItem ? $item->soldItem->user : null;
        $seller = $item->user;

        // 購入者のみ実行可能
        if (!$buyer || $user->id !== $buyer->id) {
            abort(403);
        }

        // 既に評価済みの場合はスキップ
        if ($item->ratings()->where('from_user_id', $user->id)->exists()) {
            return redirect()->route('items.list');
        }

        // 評価レコード作成
        $item->ratings()->create([
            'from_user_id' => $user->id,
            'to_user_id'   => $seller->id,
            'rating'       => $request->rating,
        ]);

        // transaction_status を buyer_completed に更新
        $item->update(['transaction_status' => 'buyer_completed']);

        // FN016: 出品者へのメール通知
        Mail::to($seller->email)->send(new TransactionCompleteMail($item, $buyer, $seller));

        // FN014: 商品一覧画面へ遷移
        return redirect()->route('items.list');
    }

    /**
     * 出品者の評価投稿（購入者を評価）
     * FN013: 購入者が完了した後にチャットを開くと自動モーダル → 評価送信
     * FN014: 評価送信後は商品一覧画面へ遷移
     */
    public function storeSeller(Item $item, RatingRequest $request)
    {
        $user   = Auth::user();
        $buyer  = $item->soldItem ? $item->soldItem->user : null;
        $seller = $item->user;

        // 出品者のみ実行可能
        if ($user->id !== $seller->id) {
            abort(403);
        }

        // buyer_completed の場合のみ評価可能
        if ($item->transaction_status !== 'buyer_completed') {
            return redirect()->route('items.list');
        }

        // 既に評価済みの場合はスキップ
        if ($item->ratings()->where('from_user_id', $user->id)->exists()) {
            return redirect()->route('items.list');
        }

        // 評価レコード作成
        $item->ratings()->create([
            'from_user_id' => $user->id,
            'to_user_id'   => $buyer->id,
            'rating'       => $request->rating,
        ]);

        // transaction_status を completed に更新
        $item->update(['transaction_status' => 'completed']);

        // FN014: 商品一覧画面へ遷移
        return redirect()->route('items.list');
    }
}
