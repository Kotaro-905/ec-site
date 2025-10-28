<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 郵便番号：必須・ハイフンあり 3-4 桁
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            // 住所：必須
            'address'     => ['required', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'postal_code' => '郵便番号',
            'address'     => '住所',
        ];
    }

    public function messages(): array
    {
        return [
            'postal_code.required' => ':attributeを入力してください。',
            'postal_code.regex'    => ':attributeは「123-4567」の形式で入力してください。',
            'address.required'     => ':attributeを入力してください。',
        ];
    }
}
