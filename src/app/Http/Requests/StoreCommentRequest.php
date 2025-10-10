<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ログインユーザーのみ許可
        return auth()->check();
    }

    // 前処理：前後の空白削除・連続空白を1つに
    protected function prepareForValidation(): void
    {
        $comment = $this->input('comment');

        if (is_string($comment)) {
            
            $comment = trim($comment);
        }

        $this->merge([
            'comment' => $comment,
        ]);
    }

    public function rules(): array
    {
        return [
            'comment' => ['required', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'comment' => '商品へのコメント',
        ];
    }

    public function messages(): array
    {
        return [
            'comment.required' => ':attributeを入力してください。',
            'comment.string'   => ':attributeは文字列で入力してください。',
            'comment.max'      => ':attributeは:max文字以内で入力してください。',
        ];
    }
}
