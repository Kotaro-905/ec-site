<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'in:card,konbini'],
            'address_id'     => [
                'required',
                'integer',
                // 自分の住所だけを有効にする
                Rule::exists('addresses', 'id')->where(
                    fn ($q) =>
                    $q->where('user_id', auth()->id())
                ),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'payment_method' => '支払い方法',
            'address_id'     => '配送先',
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => ':attributeを選択してください。',
            'payment_method.in'       => '有効な:attributeを選択してください。',
            'address_id.required'     => ':attributeを選択してください。',
            'address_id.exists'       => '有効な:attributeを選択してください。',
        ];
    }
}
