<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\OrderItem;
use App\Models\Category;              // ← 追加
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    private int $categoryId;          // ← 追加: 使い回すカテゴリID

    protected function setUp(): void
    {
        parent::setUp();

        // NOT NULL のため、テスト用に最低1件のカテゴリを作って保持
        $this->categoryId = Category::create(['name' => 'テストカテゴリ'])->id;
    }

    /* 便利関数: ユーザーを手動作成 */
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

    /* 便利関数: アイテムを手動作成 */
    private function makeItem(User $owner, array $overrides = []): Item
    {
        $base = [
            'user_id'     => $owner->id,
            'category_id' => $this->categoryId,
            'name'        => 'ITEM-'.Str::random(5),
            'brand'       => 'テストブランド',
            'description' => 'テスト説明',
            'price'       => 1000,
            'condition'   => 3,
            'image'       => 'dummy.jpg',
            'status'      => 1,
        ];

        // ❌ $base + $overrides では左側優先のため上書きできない
        // ⭕ array_merge か array_replace を使う
        return Item::create(array_merge($base, $overrides));
        // return Item::create(array_replace($base, $overrides)); でもOK
    }


    /** @test */
    public function ゲストでも商品一覧が見える()
    {
        $owner = $this->makeUser('owner');

        $this->makeItem($owner, ['name' => 'AAA']);
        $this->makeItem($owner, ['name' => 'BBB']);
        $this->makeItem($owner, ['name' => 'CCC']);

        $res = $this->get(route('items.index'));
        $res->assertOk()
            ->assertSeeText('AAA')
            ->assertSeeText('BBB')
            ->assertSeeText('CCC');
    }

    /** @test */
    public function ログイン時は自分の出品が一覧から除外される()
    {
        $me   = $this->makeUser('me');
        $else = $this->makeUser('else');

        $this->makeItem($me, ['name' => 'MY-ITEM']);
        $this->makeItem($else, ['name' => 'OTHER-ITEM']);

        $this->actingAs($me);

        $res = $this->get(route('items.index'));
        $res->assertOk();
        $res->assertDontSeeText('MY-ITEM');
        $res->assertSeeText('OTHER-ITEM');
    }

    /** @test */
    public function 購入済みの商品には_SOLD_ラベルが出る()
    {
        $buyer  = $this->makeUser('buyer');
        $seller = $this->makeUser('seller');

        $this->makeItem($seller, ['name' => 'UNSOLD_ITEM']);

        $sold = $this->makeItem($seller, [
            'name'  => 'WILL_BE_SOLD',
            'price' => 1234,
        ]);

        OrderItem::create([
         'user_id'           => $buyer->id,
         'item_id'           => $sold->id,
          'status'            => 1,
          'price_at_purchase' => $sold->price,
         'payment_method'    => 1, // 例: 1=カード、2=コンビニ…等、アプリの定義に合わせる
        ]);


        $res = $this->get(route('items.index'));
        $res->assertOk();

        $html = $res->getContent();
        $this->assertGreaterThanOrEqual(1, substr_count($html, 'SOLD'));

        $res->assertSeeText('WILL_BE_SOLD');
        $res->assertSeeText('UNSOLD_ITEM');
    }
}
