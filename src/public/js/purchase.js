const payment_method = document.getElementById('payment');
payment_method.addEventListener('change', function (e) {
    e.preventDefault();
    document.getElementById('pay_confirm').textContent = e.target.value == 'card' ? 'クレジットカード払い' : 'コンビニ払い';
});

// フォーム送信時の処理
const purchaseForm = document.getElementById('stripe-form');
const purchaseBtn = document.getElementById('purchase_btn');

if (purchaseForm && purchaseBtn) {
    purchaseBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        const selectedPaymentMethod = document.getElementById('payment').value;
        
        // 支払い方法が選択されているか確認
        if (!selectedPaymentMethod) {
            alert('支払い方法を選択してください');
            return;
        }
        
        // クレジットカード払いの場合はStripe決済へ
        if (selectedPaymentMethod === 'card') {
            purchaseForm.submit();
        } else {
            // コンビニ払いの場合
            purchaseForm.submit();
        }
    });
}

// 配送先変更機能（既存のコード）
const change_destination_btn = document.getElementById('destination__update');
const set_destination_btn = document.getElementById('destination__setting');

if (change_destination_btn && set_destination_btn) {
    change_destination_btn.addEventListener('click', (e) => {
        e.target.style.display = "none";
        set_destination_btn.style.display = "unset";
        var inputs = document.getElementsByClassName('input_destination');
        for (const input of inputs) {
            input.readOnly = false;
        }
        inputs[0].focus();
    });

    set_destination_btn.addEventListener('click', (e) => {
        e.target.style.display = "none";
        change_destination_btn.style.display = "unset";
        var inputs = document.getElementsByClassName('input_destination');
        for (const input of inputs) {
            input.readOnly = true;
        }
    });
}