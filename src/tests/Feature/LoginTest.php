<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function メールが未入力でエラー()
    {
        $res = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $res->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /** @test */
    public function パスワードが未入力でエラー()
    {
        $res = $this->post('/login', [
            'email' => 'a@example.com',
            'password' => '',
        ]);

        $res->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    /** @test */
    public function 認証情報が間違うとエラー()
    {
        $user = User::factory()->create([
            'email' => 'a@example.com',
            'password' => Hash::make('password'),
        ]);

        $res = $this->post('/login', [
            'email' => 'a@example.com',
            'password' => 'wrong',
        ]);

        $res->assertSessionHasErrors(); // Fortify のデフォルト動作
        $this->assertGuest();
    }

    /** @test */
    public function 正しい情報ならログイン成功()
    {
        $user = User::factory()->create([
            'email' => 'a@example.com',
            'password' => Hash::make('password'),
        ]);

        $res = $this->post('/login', [
            'email' => 'a@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $res->assertRedirect(); // intended(items.index) を想定
    }
}