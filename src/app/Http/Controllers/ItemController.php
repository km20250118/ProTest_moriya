<?php

namespace App\Http\Controllers;

use Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ItemRequest;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Models\CategoryItem;

class ItemController extends Controller
{
  public function index(Request $request)
  {
    $tab = $request->query('tab', 'recommend');
    $search = $request->query('search', ''); // デフォルト値を空文字列に

    $query = Item::query();

    // ログインしている場合のみ自分の商品を除外
    if (Auth::check()) {
      $query->where('user_id', '<>', Auth::id());
    }

    if ($tab === 'mylist') {
      if (!Auth::check()) {
        return redirect()->route('login');
      }

      $query->whereIn('id', function ($query) {
        $query->select('item_id')
          ->from('likes')
          ->where('user_id', auth()->id());
      });
    }

    if ($search) {
      $query->where('name', 'like', "%{$search}%");
    }

    $items = $query->get();

    return view('index', compact('items', 'tab', 'search'));
  }

  public function detail(Item $item)
  {
    return view('detail', compact('item'));
  }

  public function search(Request $request)
  {
    $search = $request->input('keyword', '');
    $tab = 'recommend';
    $query = Item::query();
    $query = Item::scopeItem($query, $search);

    $items = $query->get();
    return view('index', compact('items', 'search', 'tab'));
  }

  public function sellView()
  {
    $categories = Category::all();
    $conditions = Condition::all();
    return view('sell', compact('categories', 'conditions'));
  }

  public function sellCreate(ItemRequest $request)
  {
    // imageまたはimg_urlのどちらでも対応
    $img = $request->file('image') ?? $request->file('img_url');
    
    if ($img) {
      $img_url = Storage::disk('local')->put('public/img', $img);
    } else {
      $img_url = null; // 画像がない場合
    }
    
    $item = Item::create([
      'name' => $request->name,
      'price' => $request->price,
      'brand' => $request->brand,
      'description' => $request->description,
      'img_url' => $img_url,
      'condition_id' => $request->condition_id,
      'user_id' => Auth::id(),
    ]);
    foreach ($request->categories as $category_id) {
      CategoryItem::create([
        'item_id' => $item->id,
        'category_id' => $category_id
      ]);
    }
    return redirect()->route('item.detail', ['item' => $item->id]);
}
}
