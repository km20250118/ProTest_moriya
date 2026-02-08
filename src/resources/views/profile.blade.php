@extends('layouts.default')

<!-- タイトル -->
@section('title','プロフィール設定')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('/css/profile.css')  }}" >
@endsection

<!-- 本体 -->
@section('content')

@include('components.header')
<form action="/mypage/profile" method="post" class="profile center" enctype="multipart/form-data">
    @csrf
    <h1 class="page__title">プロフィール設定</h1>
    <div class="user">
        <div class="user__img">
            @if (isset($profile->img_url))
                <img id="myImage" class="user__icon" src="{{ \Storage::url($profile->img_url) }}" alt="">
            @else
                <img id="myImage" class="user__icon" src="{{ asset('img/icon.png') }}" alt="">
            @endif
        </div>
        <div class="profile__user--btn">
            <label class="btn2">
                画像を選択する
                <input id="target" class="btn2--input" type="file" name="img_url" accept="image/png, image/jpeg">
            </label>
        </div>
    </div>
    <label for="name" class="entry__name">ユーザ名</label>
    <input name="name" id="name" type="text" class="input" value="{{ Auth::user()->name }}">
    <div class="form__error">
        @error('name')
            {{ $message }}
        @enderror
    </div>

    <label for="postcode" class="entry__name">郵便番号</label>
    <input name="postcode" id="postcode" type="text" class="input" value="{{ $profile ? $profile->postcode : '' }}" size="10" maxlength="8" onKeyUp="AjaxZip3.zip2addr(this,'','address','address');">
    <div class="form__error">
        @error('postcode')
            {{ $message }}
        @enderror
    </div>

    <label for="address" class="entry__name">住所</label>
    <input name="address" id="address" type="text" class="input" value="{{ $profile ? $profile->address : '' }}">
    <div class="form__error">
        @error('address')
            {{ $message }}
        @enderror
    </div>

    <label for="building" class="entry__name">建物名</label>
    <input name="building" id="building" type="text" class="input" value="{{ $profile ? $profile->building : '' }}">
    <button class="btn btn--big">更新する</button>
</form>
<script>
const target = document.getElementById('target');
target.addEventListener('change', function (e) {
    const file = e.target.files[0]
    const reader = new FileReader();
    reader.onload = function (e) {
        const img = document.getElementById("myImage");
        console.log(img.src);
        img.src = e.target.result;
        console.log(img.src);
    }
    reader.readAsDataURL(file);
}, false);
</script>
@endsection