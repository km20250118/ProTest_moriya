<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\RatingController;
use App\Http\Requests\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PaymentController;

Route::get('/', [ItemController::class, 'index'])->name('items.list');
Route::get('/item/{item}', [ItemController::class, 'detail'])->name('item.detail');
Route::get('/item', [ItemController::class, 'search']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/sell', [ItemController::class, 'sellView']);
    Route::post('/sell', [ItemController::class, 'sellCreate']);
    Route::post('/item/like/{item_id}', [LikeController::class, 'create']);
    Route::post('/item/unlike/{item_id}', [LikeController::class, 'destroy']);
    Route::post('/item/comment/{item_id}', [CommentController::class, 'create']);
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'index'])->middleware('purchase')->name('purchase.index');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'purchase'])->middleware('purchase');
    Route::get('/purchase/{item_id}/success', [PurchaseController::class, 'success']);
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'address']);
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress']);
    Route::get('/mypage', [UserController::class, 'mypage']);
    Route::get('/mypage/profile', [UserController::class, 'profile']);
    Route::post('/mypage/profile', [UserController::class, 'updateProfile']);
    Route::get('/address/edit', [AddressController::class, 'edit'])->name('address.edit');
    Route::post('/address/update', [AddressController::class, 'update'])->name('address.update');

    // ─── チャット機能 ───
    Route::get('/chat/{item}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{item}', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/chat/{item}/message/{message}/edit', [ChatController::class, 'edit'])->name('chat.edit');
    Route::put('/chat/{item}/message/{message}', [ChatController::class, 'update'])->name('chat.update');
    Route::delete('/chat/{item}/message/{message}', [ChatController::class, 'destroy'])->name('chat.destroy');

    // ─── 評価機能 ───
    Route::post('/rating/{item}/buyer', [RatingController::class, 'storeBuyer'])->name('rating.buyer');
    Route::post('/rating/{item}/seller', [RatingController::class, 'storeSeller'])->name('rating.seller');
});

Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('email');
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->name('verification.notice');

Route::post('/email/verification-notification', function (Request $request) {
    $user = session()->get('unauthenticated_user');
    if (!$user) {
        return redirect()->route('login')->with('error', 'セッションが切れました。もう一度ログインしてください。');
    }
    $user->sendEmailVerificationNotification();
    session()->put('resent', true);
    return back()->with('message', 'Verification link sent!');
})->name('verification.send');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    session()->forget('unauthenticated_user');
    return redirect('/mypage/profile')->with('success', 'メール認証が完了しました。プロフィールを設定してください。');
})->name('verification.verify');

Route::get('/payment', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
Route::post('/payment/create-intent', [PaymentController::class, 'createPaymentIntent'])->name('payment.create-intent');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::post('/webhook/stripe', [PaymentController::class, 'webhook'])->name('payment.webhook');
