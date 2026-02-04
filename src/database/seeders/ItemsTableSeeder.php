<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Like;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $params = [
            // C001〜C005: user_id: 1（一般ユーザ1）
            [
                'name' => '腕時計',
                'price' => 15000,
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'img_url' => 'img/items/mens_clock.jpg',
                'user_id' => 1,
                'condition_id' => Condition::$UNUSED,
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'img_url' => 'img/items/hard_disk.jpg',
                'user_id' => 1,
                'condition_id' => Condition::$HARMLESS,
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand' => '',
                'description' => '新鮮な玉ねぎ3束のセット',
                'img_url' => 'img/items/onion.jpg',
                'user_id' => 1,
                'condition_id' => Condition::$HARMED,
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'brand' => '',
                'description' => 'クラシックなデザインの革靴',
                'img_url' => 'img/items/leather_shoes.jpg',
                'user_id' => 1,
                'condition_id' => Condition::$BAD_CONDITION,
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'brand' => '',
                'description' => '高性能なノートパソコン',
                'img_url' => 'img/items/laptop_PC.jpg',
                'user_id' => 1,
                'condition_id' => Condition::$UNUSED,
            ],
            // C006〜C010: user_id: 2（一般ユーザ2）
            [
                'name' => 'マイク',
                'price' => 8000,
                'brand' => '',
                'description' => '高音質のレコーディング用マイク',
                'img_url' => 'img/items/mic.jpg',
                'user_id' => 2,
                'condition_id' => Condition::$HARMLESS,
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand' => '',
                'description' => 'おしゃれなショルダーバッグ',
                'img_url' => 'img/items/shoulder_bag.jpg',
                'user_id' => 2,
                'condition_id' => Condition::$HARMED,
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'brand' => '',
                'description' => '使いやすいタンブラー',
                'img_url' => 'img/items/tumbler.jpg',
                'user_id' => 2,
                'condition_id' => Condition::$BAD_CONDITION,
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'img_url' => 'img/items/coffer_mill.jpg',
                'user_id' => 2,
                'condition_id' => Condition::$UNUSED,
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'brand' => '',
                'description' => '便利なメイクアップセット',
                'img_url' => 'img/items/makeup-set.jpg',
                'user_id' => 2,
                'condition_id' => Condition::$HARMLESS,
            ],
        ];

        $range = count($params);
        for ($i = 0; $i < $range; $i++) {
            Item::create($params[$i]);
        }

        // いいね機能のダミーデータ
        Like::create([
            'user_id' => 1,
            'item_id' => 6,
        ]);
        Like::create([
            'user_id' => 2,
            'item_id' => 1,
        ]);
    }
}
