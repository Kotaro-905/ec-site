<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Tests\TestCase;

class EmailVerificationFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 登録時に認証メールが送られる()
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'taro',
            'email' => 'taro@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('verification.notice'));

        $user = User::whereEmail('taro@example.com')->first();
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /** @test */
    public function 誘導画面が表示できる_ログイン必須()
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('verification.notice'))
            ->assertOk()
            ->assertSee('メール認証'); // 文言は your blade に合わせてOK
    }

    /** @test */
    public function 誘導画面の認証ボタンで署名付きURLにリダイレクトされ_アクセスすると認証完了する()
    {
        Event::fake(); // Verified イベント検証のため

        $user = User::factory()->unverified()->create();

        // ① 誘導画面 → 認証ボタン押下（署名URLへ）
        $urlRes = $this->actingAs($user)->post(route('verification.perform'));
        $urlRes->assertRedirect();
        $signedUrl = $urlRes->headers->get('Location');
        $this->assertNotNull($signedUrl);

        // ② 署名付きURLにアクセス → 本家 verify（ミドルウェア signed, auth）
        $verifyRes = $this->actingAs($user)->get($signedUrl);
        $verifyRes->assertRedirect(route('items.index'));

        $user->refresh();
        $this->assertTrue($user->hasVerifiedEmail());

        Event::assertDispatched(Verified::class);
    }
}
