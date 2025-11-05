<?php

namespace Database\Factories;

use App\Models\Condition;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConditionFactory extends Factory
{
    protected $model = Condition::class;

    public function definition()
    {
        return [
            'condition' => $this->faker->randomElement([
                '新品、未使用',
                '未使用に近い',
                '目立った傷や汚れなし',
                'やや傷や汚れあり',
            ]),
        ];
    }
}
