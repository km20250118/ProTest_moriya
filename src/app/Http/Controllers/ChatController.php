<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChatMessageRequest;
use App\Models\ChatMessage;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 取引チャット画面表示
     * FN001~FN005, FN006, FN009, FN010, FN011, FN012, FN013
     */
    public function show(Item $item)
    {
        $user   = Auth::user();
        $seller = $item->user;
        $buyer  = $item->soldItem ? $item->soldItem->user : null;

        // アクセス権限: 購入者か出品者のみ
        if (!$buyer || ($user->id !== $seller->id && $user->id !== $buyer->id)) {
            abort(403);
        }

        $isBuyer = ($user->id === $buyer->id);

        // メッセージを古い順に取得
        $messages = $item->chatMessages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // 読み済みに更新
        if ($isBuyer) {
            $item->chatMessages()->where('read_by_buyer', false)->update(['read_by_buyer' => true]);
        } else {
            $item->chatMessages()->where('read_by_seller', false)->update(['read_by_seller' => true]);
        }

        // サイドバー用: 取引中の全商品（新規メッセージ順）
        $transactionItems = $this->getTransactionItems($user);

        // 出品者向け取引完了モーダル判定
        // FN013: 購入者が完了済みで、出品者がまだ評価していない場合に表示
        $showRatingModal = false;
        if (!$isBuyer && $item->transaction_status === 'buyer_completed') {
            $showRatingModal = !$item->ratings()
                ->where('from_user_id', $seller->id)
                ->exists();
        }

        return view('chat.show', [
            'item'               => $item,
            'messages'           => $messages,
            'transactionItems'   => $transactionItems,
            'isBuyer'            => $isBuyer,
            'buyer'              => $buyer,
            'seller'             => $seller,
            'showRatingModal'    => $showRatingModal,
        ]);
    }

    /**
     * メッセージ投稿
     * FN006: 本文（必須）・画像
     */
    public function store(Item $item, ChatMessageRequest $request)
    {
        $user   = Auth::user();
        $seller = $item->user;
        $buyer  = $item->soldItem ? $item->soldItem->user : null;

        if (!$buyer || ($user->id !== $seller->id && $user->id !== $buyer->id)) {
            abort(403);
        }

        $isBuyer   = ($user->id === $buyer->id);
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_images', 'public');
        }

        ChatMessage::create([
            'item_id'          => $item->id,
            'user_id'          => $user->id,
            'body'             => $request->body,
            'image'            => $imagePath,
            'read_by_buyer'    => $isBuyer,   // 送信者側は読み済み
            'read_by_seller'   => !$isBuyer,
        ]);

        // FN009: 送信成功時にdraftをクリアするためのフラッシュ
        return redirect()->route('chat.show', $item)->with('message_sent', true);
    }

    /**
     * メッセージ編集画面
     * FN010
     */
    public function edit(Item $item, ChatMessage $message)
    {
        if ($message->user_id !== Auth::id()) {
            abort(403);
        }

        return view('chat.edit', [
            'item'    => $item,
            'message' => $message,
        ]);
    }

    /**
     * メッセージ更新
     * FN010
     */
    public function update(Item $item, ChatMessage $message, ChatMessageRequest $request)
    {
        if ($message->user_id !== Auth::id()) {
            abort(403);
        }

        $imagePath = $message->image;

        if ($request->hasFile('image')) {
            // 古い画像を削除
            if ($message->image) {
                Storage::disk('public')->delete($message->image);
            }
            $imagePath = $request->file('image')->store('chat_images', 'public');
        }

        $message->update([
            'body'  => $request->body,
            'image' => $imagePath,
        ]);

        return redirect()->route('chat.show', $item);
    }

    /**
     * メッセージ削除（ソフトデリーテート）
     * FN011
     */
    public function destroy(Item $item, ChatMessage $message)
    {
        if ($message->user_id !== Auth::id()) {
            abort(403);
        }

        $message->delete();

        return redirect()->route('chat.show', $item);
    }

    /**
     * 取引中の商品一覧を取得（サイドバー・マイページ共用）
     * FN001, FN004: 新規メッセージが来た順にソート
     * FN005: 各商品の未読メッセージ数を付与
     *
     * @param  object $user
     * @return \Illuminate\Support\Collection
     */
    public function getTransactionItems(object $user): \Illuminate\Support\Collection
    {
        // 出品して取引中の商品
        $sellingItems = Item::where('user_id', $user->id)
            ->whereIn('transaction_status', ['in_transaction', 'buyer_completed'])
            ->with('chatMessages.user', 'soldItem.user')
            ->get();

        // 購入して取引中の商品
        $buyingItems = Item::whereHas('soldItem', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->whereIn('transaction_status', ['in_transaction', 'buyer_completed'])
            ->with('chatMessages.user', 'user')
            ->get();

        $allItems = $sellingItems->merge($buyingItems);

        // 各商品に未読数・最新メッセージを付与
        $allItems->each(function ($item) use ($user) {
            $isBuyer = ($item->soldItem && $item->soldItem->user_id === $user->id);
            $readKey = $isBuyer ? 'read_by_buyer' : 'read_by_seller';

            $item->unread_count    = $item->chatMessages->where($readKey, false)->count();
            $item->latest_message  = $item->chatMessages->sortByDesc('created_at')->first();
            $item->is_buyer        = $isBuyer;
        });

        // FN004: 最新メッセージ日時でソート
        return $allItems->sortByDesc(function ($item) {
            return $item->latest_message
                ? $item->latest_message->created_at
                : $item->created_at;
        });
    }
}
