<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RatingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'rating' => 'required|integer|between:1,5',
        ];
    }

    public function messages()
    {
        return [
            'rating.required' => '評価を選んでください',
            'rating.integer'  => '不正な入力です',
            'rating.between'  => '評価は1以上5以下の値を選んでください',
        ];
    }
}
