@extends('layouts.default')

@section('content')
<div class="container" style="max-width: 600px; padding-top: 40px;">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0" style="font-size: 15px;">メッセージ編集</h5>
            <a href="{{ route('chat.show', $item) }}" class="btn btn-sm btn-outline-secondary">キャンセル</a>
        </div>
        <div class="card-body">

            <!-- バリデーションエラー (FN008) -->
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="text-danger mb-1" style="font-size: 12px;">{{ $error }}</div>
                @endforeach
            @endif

            <form action="{{ route('chat.update', [$item, $message]) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- 本文 -->
                <div class="mb-3">
                    <label for="editBody" class="form-label" style="font-size: 13px; font-weight: 600;">本文</label>
                    <textarea name="body"
                              id="editBody"
                              class="form-control"
                              rows="4"
                              maxlength="400"
                              style="font-size: 13px;">{{ old('body', $message->body) }}</textarea>
                    <div class="text-end text-muted" style="font-size: 12px; margin-top: 4px;">
                        <span id="editCharCount">{{ mb_strlen(old('body', $message->body)) }}</span> / 400 文字
                    </div>
                </div>

                <!-- 画像 -->
                <div class="mb-3">
                    <label class="form-label" style="font-size: 13px; font-weight: 600;">画像</label>

                    <!-- 現在の画像プレビュー -->
                    @if ($message->image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $message->image) }}"
                                 alt="現在の画像"
                                 style="max-width: 180px; border-radius: 8px; border: 1px solid #eee;">
                            <p class="text-muted mb-0 mt-1" style="font-size: 11px;">現在の画像（新規アップロードで上書き）</p>
                        </div>
                    @endif

                    <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png" style="font-size: 13px;">
                    <div class="text-muted mt-1" style="font-size: 11px;">.png または .jpeg 形式のファイルをアップロートできます</div>
                </div>

                <!-- 送信 -->
                <div class="text-end">
                    <button type="submit" class="btn btn-primary" style="font-size: 13px; padding: 8px 22px;">
                        更新
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// 文字数カウント
(function () {
    var textarea   = document.getElementById('editBody');
    var charCount  = document.getElementById('editCharCount');

    textarea.addEventListener('input', function () {
        charCount.textContent = this.value.length;
    });
})();
</script>
@endsection
