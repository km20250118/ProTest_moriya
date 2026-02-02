@extends('layouts.default')

<!-- タイトル -->
@section('title','マイページ')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('/css/index.css')  }}" >
<link rel="stylesheet" href="{{ asset('/css/mypage.css')  }}" >
@endsection

<!-- 本体 -->
@section('content')

@include('components.header')
<div class="container">
    <div class="user">
           <div class="user__info">
    <div class="user__img">
        @if (isset($user->profile->img_url))
            <img class="user__icon" src="{{ \Storage::url($user->profile->img_url) }}" alt="">
        @else
            <img id="myImage" class="user__icon" src="{{ asset('img/icon.png') }}" alt="">
        @endif
    </div>
    <div class="user__text">
        <p class="user__name">{{$user->name}}</p>
        @php
            $ratingAvg = $user->getRatingAverage();
        @endphp
        @if ($ratingAvg)
            <div class="user-rating">
                @for ($i = 1; $i <= 5; $i++)
                    <span class="star {{ $i <= $ratingAvg ? 'filled' : '' }}">★</span>
                @endfor
            </div>
        @endif
    </div>
</div>
            <div class="mypage__user--btn">
            <a class="btn2" href="/mypage/profile">プロフィールを編集</a>
            </div>
    </div>
    <div class="border">
        <ul class="border__list">
            <li>
                <a href="/mypage?page=sell" 
                   class="{{ $currentTab === 'sell' ? 'active' : '' }}">
                    出品した商品
                </a>
            </li>
            <li>
                <a href="/mypage?page=buy" 
                   class="{{ $currentTab === 'buy' ? 'active' : '' }}">
                    購入した商品
                </a>
            </li>
            <li>
                <a href="/mypage?page=transaction" 
                   class="{{ $currentTab === 'transaction' ? 'active' : '' }} {{ $transactionItems->sum('unread_count') > 0 ? 'has-unread' : '' }}">
                   取引中の商品
                   @if ($transactionItems->sum('unread_count') > 0)
                       <span class="unread-badge">{{ $transactionItems->sum('unread_count') }}</span>
                   @endif
                </a>
            </li>
        </ul>
    </div>

    @if ($currentTab === 'transaction')
        {{-- 取引中商品タブ（グリッド表示） --}}
        @include('mypage._transaction_items', ['transactionItems' => $transactionItems])
    @else
        {{-- 既存の商品一覧 --}}
        <div class="items">
            @foreach ($items as $item)
            <div class="item">
                <a href="/item/{{$item->id}}">
                    @if ($item->sold())
                        <div class="item__img--container sold">
                            <img src="{{ \Storage::url($item->img_url) }}" class="item__img" alt="商品画像">
                        </div>
                    @else
                        <div class="item__img--container">
                            <img src="{{ \Storage::url($item->img_url) }}" class="item__img" alt="商品画像">
                        </div>
                    @endif
                    <p class="item__name">{{$item->name}}</p>
                </a>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection