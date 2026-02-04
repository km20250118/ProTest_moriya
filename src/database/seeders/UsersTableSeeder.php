<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 一般ユーザ1（C001〜C005の商品を出品）
        $param = [
            'name' => '一般ユーザ1',
            'email' => 'general1@gmail.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('password'),
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => null,
        ];
        User::create($param);

        // 一般ユーザ2（C006〜C010の商品を出品）
        $param = [
            'name' => '一般ユーザ2',
            'email' => 'general2@gmail.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('password'),
            'postal_code' => '234-5678',
            'address' => '東京都新宿区2-2-2',
            'building' => null,
        ];
        User::create($param);

        // 一般ユーザ3（商品なし）
        $param = [
            'name' => '一般ユーザ3',
            'email' => 'general3@gmail.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('password'),
            'postal_code' => '345-6789',
            'address' => '東京都世田谷区3-3-3',
            'building' => null,
        ];
        User::create($param);
    }
}
