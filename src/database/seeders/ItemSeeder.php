<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Category;
use Illuminate\Support\Collection;

class ItemSeeder extends Seeder
{
    /**
     * @param  Collection|null  $users  // DatabaseSeeder::callWith から渡す想定
     */
    public function run(Collection $users = null): void
    {
        $now = now(); // Carbon::now() と同じ

        // 1) ユーザーIDを用意（渡されなければDBから）
        $userIds = $users
            ? $users->pluck('id')->all()
            : DB::table('users')->pluck('id')->all();

        if (empty($userIds)) {
            throw new \RuntimeException('ユーザーが存在しません。先に UserSeeder を実行してください。');
        }

        // 2) カテゴリ name->id マップ
        $catIdByName = Category::query()
            ->pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [trim($name) => $id]);

        // 3) 商品→カテゴリ名のマップ
        $catMapByName = [
            '腕時計'          => ['メンズ', 'アクセサリー'],
            'HDD'             => ['家電', 'インテリア'],
            '玉ねぎ3束'       => ['キッチン'],
            '革靴'            => ['ファッション', 'メンズ'],
            'ノートPC'        => ['家電'],
            'マイク'          => ['家電'],
            'ショルダーバッグ' => ['ファッション', 'レディース'],
            'タンブラー'       => ['キッチン', 'インテリア'],
            'コーヒーミル'     => ['キッチン', 'インテリア'],
            'メイクセット'     => ['コスメ', 'レディース'],
        ];

        // 4) 商品データ（brand は未入力を null に）
        $items = [
            ['name'=>'腕時計','price'=>15000,'brand'=>'Rolax','description'=>'スタイリッシュなデザインのメンズ腕時計','image'=>'Armani+Mens+Clock.jpg','condition'=>1,'status'=>1],
            ['name'=>'HDD','price'=>5000,'brand'=>'西芝','description'=>'高速で信頼性の高いハードディスク','image'=>'HDD+Hard+Disk.jpg','condition'=>2,'status'=>1],
            ['name'=>'玉ねぎ3束','price'=>300,'brand'=>null,'description'=>'新鮮な玉ねぎ3束のセット','image'=>'iLoveIMG+d.jpg','condition'=>3,'status'=>1],
            ['name'=>'革靴','price'=>4000,'brand'=>null,'description'=>'クラシックなデザインの革靴','image'=>'Leather+Shoes+Product+Photo.jpg','condition'=>4,'status'=>1],
            ['name'=>'ノートPC','price'=>45000,'brand'=>null,'description'=>'高性能なノートパソコン','image'=>'Living+Room+Laptop.jpg','condition'=>1,'status'=>1],
            ['name'=>'マイク','price'=>8000,'brand'=>null,'description'=>'高音質のレコーディング用マイク','image'=>'Music+Mic+4632231.jpg','condition'=>2,'status'=>1],
            ['name'=>'ショルダーバッグ','price'=>3500,'brand'=>null,'description'=>'おしゃれなショルダーバッグ','image'=>'Purse+fashion+pocket.jpg','condition'=>3,'status'=>1],
            ['name'=>'タンブラー','price'=>500,'brand'=>null,'description'=>'使いやすいタンブラー','image'=>'Tumbler+souvenir.jpg','condition'=>4,'status'=>1],
            ['name'=>'コーヒーミル','price'=>4000,'brand'=>'Starbacks','description'=>'手動のコーヒーミル','image'=>'Waitress+with+Coffee+Grinder.jpg','condition'=>1,'status'=>1],
            ['name'=>'メイクセット','price'=>2500,'brand'=>null,'description'=>'便利なメイクアップセット','image'=>'Makeup+Set.jpg','condition'=>2,'status'=>1],
        ];

        DB::transaction(function () use ($items, $catMapByName, $catIdByName, $userIds, $now) {
            foreach ($items as $row) {
                // カテゴリ解決
                $catIds = collect($catMapByName[$row['name']] ?? [])
                    ->map(fn ($n) => $catIdByName[trim($n)] ?? null)
                    ->filter()
                    ->values()
                    ->all();

                if (empty($catIds)) {
                    throw new \RuntimeException("カテゴリ未解決: {$row['name']}");
                }

                // main レコード
                $row['user_id']     = $userIds[array_rand($userIds)];
                $row['category_id'] = $catIds[0];
                $row['created_at']  = $now;
                $row['updated_at']  = $now;

                $itemId = DB::table('items')->insertGetId($row);

                // pivot まとめ挿入
                $pivot = array_map(fn ($cid) => [
                    'item_id' => $itemId,
                    'category_id' => $cid,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $catIds);

                DB::table('item_categories')->insert($pivot);
            }
        });
    }
}
