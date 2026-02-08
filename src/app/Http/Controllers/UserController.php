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
    $profile = Profile::where('user_id', Auth::id())->first();
    
    // 画像処理
    $img_url = null;
    if ($request->hasFile('img_url')) {
      \Log::info("画像アップロード: " . $request->file("img_url")->getClientOriginalName());
      $img_url = Storage::disk('local')->put('public/img', $request->file('img_url'));
    }
    
    $profileData = [
      'user_id' => Auth::id(),
      'postcode' => $request->postcode,
      'address' => $request->address,
      'building' => $request->building
    ];
    
    // 画像がアップロードされた場合のみimg_urlを更新
    if ($img_url) {
      $profileData['img_url'] = $img_url;
    }
    
    if ($profile) {
      $profile->update($profileData);
    } else {
      // 新規作成の場合は画像がなくても空文字列を設定
      if (!$img_url) {
        $profileData['img_url'] = '';
      }
      Profile::create($profileData);
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
