<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Category;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // categories の name→id マップ
        $catIdByName = Category::pluck('id', 'name');

        // 商品名→カテゴリ名
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

        $items = [
            [
                'name'        => '腕時計',
                'price'       => 15000,
                'brand'       => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image'       => 'Armani+Mens+Clock.jpg',
                'condition'   => 1, // 良好
                'status'      => 1, // 出品中
            ],
            [
                'name'        => 'HDD',
                'price'       => 5000,
                'brand'       => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'image'       => 'HDD+Hard+Disk.jpg',
                'condition'   => 2,
                'status'      => 1,
            ],
            [
                'name'        => '玉ねぎ3束',
                'price'       => 300,
                'brand'       => 'なし',
                'description' => '新鮮な玉ねぎ3束のセット',
                'image'       => 'iLoveIMG+d.jpg',
                'condition'   => 3,
                'status'      => 1,
            ],
            [
                'name'        => '革靴',
                'price'       => 4000,
                'brand'       => 'なし',
                'description' => 'クラシックなデザインの革靴',
                'image'       => 'Leather+Shoes+Product+Photo.jpg',
                'condition'   => 4,
                'status'      => 2,
            ],
            [
                'name'        => 'ノートPC',
                'price'       => 45000,
                'brand'       => 'なし',
                'description' => '高性能なノートパソコン',
                'image'       => 'Living+Room+Laptop.jpg',
                'condition'   => 1,
                'status'      => 1,
            ],
            [
                'name'        => 'マイク',
                'price'       => 8000,
                'brand'       => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'image'       => 'Music+Mic+4632231.jpg',
                'condition'   => 2,
                'status'      => 1,
            ],
            [
                'name'        => 'ショルダーバッグ',
                'price'       => 3500,
                'brand'       => 'なし',
                'description' => 'おしゃれなショルダーバッグ',
                'image'       => 'Purse+fashion+pocket.jpg',
                'condition'   => 3,
                'status'      => 1,
            ],
            [
                'name'        => 'タンブラー',
                'price'       => 500,
                'brand'       => 'なし',
                'description' => '使いやすいタンブラー',
                'image'       => 'Tumbler+souvenir.jpg',
                'condition'   => 4,
                'status'      => 1,
            ],
            [
                'name'        => 'コーヒーミル',
                'price'       => 4000,
                'brand'       => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'image'       => 'Waitress+with+Coffee+Grinder.jpg',
                'condition'   => 1,
                'status'      => 1,
            ],
            [
                'name'        => 'メイクセット',
                'price'       => 2500,
                'brand'       => 'なし',
                'description' => '便利なメイクアップセット',
                'image'       => 'Makeup+Set.jpg',
                'condition'   => 2,
                'status'      => 1,
            ],
        ];

        foreach ($items as $row) {
            $catIds = collect($catMapByName[$row['name']] ?? [])
                ->map(fn($n) => $catIdByName[$n] ?? null)
                ->filter()
                ->values()
                ->all();

            if (empty($catIds)) {
                throw new \RuntimeException("カテゴリ未解決: {$row['name']}");
            }

            $row['category_id'] = $catIds[0]; // 主カテゴリ
            $row['created_at']  = $now;
            $row['updated_at']  = $now;

            $itemId = DB::table('items')->insertGetId($row);

            $pivot = [];
            foreach ($catIds as $cid) {
                $pivot[] = [
                    'item_id'     => $itemId,
                    'category_id' => $cid,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
            DB::table('item_categories')->insert($pivot);
        }
    }
}
