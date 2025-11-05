<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AddressController extends Controller
{
    // 住所編集画面
    public function edit(Request $request)
    {
        $user = Auth::user();
        $item_id = $request->item_id;
        return view('address.edit', compact('user', 'item_id'));
    }
    // 住所更新処理
    public function update(Request $request)
    {
        $request->validate([
            'postcode' => 'required',
            'address' => 'required',
            'building' => 'nullable',
        ]);
        $user = Auth::user();
        
        // profilesテーブルに保存
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'postcode' => $request->postcode,
                'address' => $request->address,
                'building' => $request->building,
            ]
        );
        
        // item_idがあれば購入ページへ、なければマイページへ
        if ($request->item_id) {
            return redirect()->route('purchase.index', ['item_id' => $request->item_id])
                ->with('success', '住所を更新しました。');
        }
        
        return redirect('/mypage')
            ->with('success', '住所を更新しました。');
    }
}
