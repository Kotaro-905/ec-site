<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 名前が未入力だとエラーになる()
    {
        $res = $this->post('/register', [
            'name' => '',
            'email' => 'a@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $res->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function メールが未入力だとエラーになる()
    {
        $res = $this->post('/register', [
            'name' => 'taro',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $res->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function パスワード未入力でエラーになる()
    {
        $res = $this->post('/register', [
            'name' => 'taro',
            'email' => 'a@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $res->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function パスワード7文字以下でエラー()
    {
        $res = $this->post('/register', [
            'name' => 'taro',
            'email' => 'a@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $res->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function パスワード確認不一致でエラー()
    {
        $res = $this->post('/register', [
            'name' => 'taro',
            'email' => 'a@example.com',
            'password' => 'password',
            'password_confirmation' => 'PASSWORD',
        ]);

        $res->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function 正しく登録できると認証誘導画面へリダイレクトされる()
    {
        $res = $this->post('/register', [
            'name' => 'taro',
            'email' => 'taro@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // Fortify の RegisterResponse を上書きしている（verification.notice へ）
        $res->assertRedirect(route('verification.notice'));
        $this->assertDatabaseHas('users', ['email' => 'taro@example.com']);
        $this->assertAuthenticated();
    }
}

