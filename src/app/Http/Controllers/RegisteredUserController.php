<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class RegisteredUserController extends Controller
{
  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8|confirmed',
    ], [
      // ユーザー名のバリデーションメッセージ
      'name.required' => 'お名前を入力してください',
      'name.string' => 'お名前は文字列で入力してください',
      'name.max' => 'お名前は255文字以内で入力してください',

      // メールアドレスのバリデーションメッセージ
      'email.required' => 'メールアドレスを入力してください',
      'email.email' => 'メールアドレスはメール形式で入力してください',
      'email.max' => 'メールアドレスは255文字以内で入力してください',
      'email.unique' => 'このメールアドレスは既に登録されています',

      // パスワードのバリデーションメッセージ
      'password.required' => 'パスワードを入力してください',
      'password.string' => 'パスワードは文字列で入力してください',
      'password.min' => 'パスワードは8文字以上で入力してください',
      'password.confirmed' => 'パスワードと一致しません',
    ]);

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);

    event(new Registered($user));

    session()->put('unauthenticated_user', $user);

    return view('auth.verify-email');
  }
}
