<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '決済ページ')</title>

    {{-- ✅ Laravel MixでビルドされたCSS/JSを読み込み --}}
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>
<body>
    <main class="max-w-2xl mx-auto p-6">
        @yield('content')
    </main>
</body>
</html>
