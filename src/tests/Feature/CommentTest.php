<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    private int $categoryId;

    protected function setUp(): void
    {
        parent::setUp();
        // items.category_id が NOT NULL のため、テスト用カテゴリを1件用意
        $this->categoryId = Category::create(['name' => 'テストカテゴリ'])->id;
    }

    // ------- 便利メソッド --------
    private function makeUser(string $name = 'user'): User
    {
        static $seq = 1;
        return User::create([
            'name'     => $name,
            'email'    => sprintf('%s_%d@example.com', $name, $seq++),
            'password' => Hash::make('password'),
            'image'    => null,
        ]);
    }

    private function makeItem(User $owner, array $overrides = []): Item
    {
        $base = [
            'user_id'     => $owner->id,
            'category_id' => $this->categoryId,
            'name'        => 'ITEM-'.Str::random(5),
            'brand'       => 'テストブランド',   // ← NOT NULL なら必須
            'description' => 'テスト説明',
            'price'       => 1000,
            'condition'   => 3,
            'image'       => 'dummy.jpg',
            'status'      => 1,
        ];
        return Item::create(array_merge($base, $overrides));
    }
    // ----------------------------

    /** @test */
    public function 未ログインはコメント投稿できない()
    {
        $owner = $this->makeUser('owner');
        $item  = $this->makeItem($owner);

        $res = $this->post(route('items.comments.store', $item), [
            'comment' => 'hello',
        ]);

        $res->assertRedirect(); // loginへ
        $this->assertGuest();
    }

    /** @test */
    public function コメント必須_未入力はエラー()
    {
        $user  = $this->makeUser('u1');
        $owner = $this->makeUser('owner');
        $item  = $this->makeItem($owner);

        $this->actingAs($user);

        $res = $this->post(route('items.comments.store', $item), [
            'comment' => '',
        ]);

        $res->assertSessionHasErrors(['comment']);
    }

    /** @test */
    public function コメントは最大255文字_超過はエラー()
    {
        $user  = $this->makeUser('u1');
        $owner = $this->makeUser('owner');
        $item  = $this->makeItem($owner);

        $this->actingAs($user);

        $res = $this->post(route('items.comments.store', $item), [
            'comment' => str_repeat('あ', 256),
        ]);

        $res->assertSessionHasErrors(['comment']);
    }

    /** @test */
    public function 正常にコメントできる()
    {
        $user  = $this->makeUser('u1');
        $owner = $this->makeUser('owner');
        $item  = $this->makeItem($owner, ['name' => '対象商品']);

        $this->actingAs($user);

        $res = $this->post(route('items.comments.store', $item), [
            'comment' => 'テストコメント',
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'テストコメント',
        ]);
    }
}
