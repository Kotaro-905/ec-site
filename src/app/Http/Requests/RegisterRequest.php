<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // ユーザー名：必須・20文字以内
            'name'                  => ['required', 'string', 'max:20'],

            // メールアドレス：必須・メール形式
            'email'                 => ['required', 'email'],

            // パスワード：必須・8文字以上
            'password'              => ['required', 'string', 'min:8', 'confirmed'],

            // 確認用パスワード：必須・8文字以上・パスワードと一致（confirmedで検証）
            'password_confirmation' => ['required', 'string', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'お名前を入力してください',
            'name.max'       => 'お名前は20文字以内で入力してください',

            'email.required' => 'メールアドレスを入力してください',
            'email.email'    => 'メールアドレスはメール形式で入力してください',

            'password.required'  => 'パスワードを入力してください',
            'password.min'       => 'パスワードは8文字以上で入力してください',
            'password.confirmed' => 'パスワードと一致しません',

            'password_confirmation.required' => '確認用パスワードを入力してください',
            'password_confirmation.min'      => 'パスワードは8文字以上で入力してください',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'                  => 'ユーザー名',
            'email'                 => 'メールアドレス',
            'password'              => 'パスワード',
            'password_confirmation' => '確認用パスワード',
        ];
    }
}

