@extends('layouts.default')

<!-- タイトル -->
@section('title','トップページ')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('/css/index.css')  }}">
@endsection

<!-- 本体 -->
@section('content')

@include('components.header')
<div class="border">
    <ul class="border__list">
        <li>
            <a href="{{ route('items.list', ['tab'=>'recommend', 'search'=>$search]) }}" 
               class="{{ $tab === 'recommend' ? 'active' : '' }}">
                おすすめ
            </a>
        </li>
        @if(!auth()->guest())
        <li>
            <a href="{{ route('items.list', ['tab'=>'mylist', 'search'=>$search]) }}" 
               class="{{ $tab === 'mylist' ? 'active' : '' }}">
                マイリスト
            </a>
        </li>
        @endif
    </ul>
</div>
<div class="container">
    <div class="items">
        @foreach ($items as $item)
        <div class="item">
            <a href="/item/{{$item->id}}">
                @if ($item->sold())
                <div class="item__img--container sold">
                    <img src="{{ asset('storage/' . str_replace('public/', '', $item->img_url)) }}" class="item__img" alt="商品画像">
                </div>
                @else
                <div class="item__img--container">
                    <img src="{{ asset('storage/' . str_replace('public/', '', $item->img_url)) }}" class="item__img" alt="商品画像">
                </div>
                @endif
                <p class="item__name">{{$item->name}}</p>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection