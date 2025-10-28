<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // プロフィール画像：拡張子 jpeg/png（任意。必須にしたい場合は required に）
            'image'       => ['nullable', 'file', 'mimes:jpeg,png', 'max:5120'],
            // ユーザー名：必須・20文字以内
            'name'        => ['required', 'string', 'max:20'],
            // 郵便番号：必須・ハイフンあり
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            // 住所：必須
            'address'     => ['required', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'image'       => 'プロフィール画像',
            'name'        => 'ユーザー名',
            'postal_code' => '郵便番号',
            'address'     => '住所',
        ];
    }

    public function messages(): array
    {
        return [
            'image.mimes'     => ':attributeはjpegもしくはpngを指定してください。',
            'name.required'   => ':attributeを入力してください。',
            'name.max'        => ':attributeは:max文字以内で入力してください。',
            'postal_code.required' => ':attributeを入力してください。',
            'postal_code.regex'    => ':attributeは「123-4567」の形式で入力してください。',
            'address.required' => ':attributeを入力してください。',
        ];
    }
}
