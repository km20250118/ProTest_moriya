@extends('layouts.default')

@section('title', '住所の変更')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/purchase.css') }}">
@endsection

@section('content')
@include('components.header')
<div class="container">
    <div class="address-change-container">
        <h2 class="address-change-title">住所の変更</h2>

        <form action="/purchase/address/{{ $item_id }}" method="POST">
          @csrf

            <div class="address-form-group">
                <label>郵便番号</label>
                <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code ?? '') }}">
            </div>

            <div class="address-form-group">
                <label>住所</label>
                <input type="text" name="address" value="{{ old('address', $user->address ?? '') }}">
            </div>

            <div class="address-form-group">
                <label>建物名</label>
                <input type="text" name="building" value="{{ old('building', $user->building ?? '') }}">
            </div>

            <button type="submit" class="address-update-button">更新する</button>
        </form>
    </div>
</div>
@endsection
