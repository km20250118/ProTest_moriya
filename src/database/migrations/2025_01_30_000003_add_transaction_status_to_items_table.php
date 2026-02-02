<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            // transaction_status の遷移:
            //   null              → 未購入
            //   'in_transaction'  → 購入済み・取引チャット中
            //   'buyer_completed' → 購入者が取引完了・出品者の評価待ち
            //   'completed'       → 双方の評価完了
            $table->string('transaction_status', 20)->nullable()->after('condition_id');
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('transaction_status');
        });
    }
};
