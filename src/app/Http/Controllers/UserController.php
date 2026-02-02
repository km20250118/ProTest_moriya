<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use App\Models\User;
use App\Models\Item;
use App\Models\SoldItem;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ChatController;

class UserController extends Controller
{
  public function profile()
  {
    $profile = Profile::where('user_id', Auth::id())->first();
    return view('profile', compact('profile'));
  }

  public function updateProfile(ProfileRequest $request)
  {
    $img = $request->file('img_url');
    if (isset($img)) {
      $img_url = Storage::disk('local')->put('public/img', $img);
    } else {
      $img_url = '';
    }

    $profile = Profile::where('user_id', Auth::id())->first();
    if ($profile) {
      $profile->update([
        'user_id' => Auth::id(),
        'img_url' => $img_url,
        'postcode' => $request->postcode,
        'address' => $request->address,
        'building' => $request->building
      ]);
    } else {
      Profile::create([
        'user_id' => Auth::id(),
        'img_url' => $img_url,
        'postcode' => $request->postcode,
        'address' => $request->address,
        'building' => $request->building
      ]);
    }

    User::find(Auth::id())->update([
      'name' => $request->name
    ]);

    return redirect('/');
  }

  public function mypage(Request $request)
  {
    $user = User::find(Auth::id());
    $currentTab = $request->page ?? 'sell';

    // ─── 取引中商品データの取得（全タブで使用） ───
    $chatController = new ChatController();
    $transactionItems = $chatController->getTransactionItems($user);

    // ─── 各タブのデータ取得 ───
    if ($currentTab == 'buy') {
      $items = SoldItem::where('user_id', $user->id)->get()->map(function ($sold_item) {
        return $sold_item->item;
      });
    } elseif ($currentTab == 'transaction') {
      // 取引中商品タブ
      $items = $transactionItems;
    } else {
      // 出品した商品タブ（デフォルト）
      $items = Item::where('user_id', $user->id)->get();
    }

    return view('mypage', compact('user', 'items', 'currentTab', 'transactionItems'));
  }
}
