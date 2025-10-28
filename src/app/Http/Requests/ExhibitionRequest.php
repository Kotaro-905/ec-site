<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExhibitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'image'       => ['required','file','mimes:jpeg,png','max:5120'], // 5MB
            'name'        => ['required','string','max:255'],
            'brand'       => ['nullable','string','max:255'],
            'description' => ['required','string','max:1000'],
            'price'       => ['required','integer','min:1'],

            // ← 複数カテゴリ
            'categories'      => ['required','array','min:1'],
            'categories.*'    => ['integer','exists:categories,id'],

            'condition'   => ['required','integer', Rule::in([1,2,3,4,5])],
        ];
    }

    public function attributes(): array
    {
        return [
            'image'       => '商品画像',
            'name'        => '商品名',
            'brand'       => 'ブランド名',
            'description' => '商品説明',
            'price'       => '販売価格',
            'categories'  => 'カテゴリー',
            'condition'   => '商品の状態',
        ];
    }

    public function messages(): array
    {
        return [
            'image.required'       => ':attributeをアップロードしてください。',
            'image.mimes'          => ':attributeはjpegまたはpngを指定してください。',
            'name.required'        => ':attributeを入力してください。',
            'description.required' => ':attributeを入力してください。',
            'price.required'       => ':attributeを入力してください。',
            'price.integer'        => ':attributeは数値で入力してください。',
            'price.min'            => ':attributeは:min円以上で入力してください。',
            'categories.required'  => ':attributeを1つ以上選択してください。',
            'condition.required'   => ':attributeを選択してください。',
            'condition.in'         => ':attributeの値が不正です。',
        ];
    }
}
