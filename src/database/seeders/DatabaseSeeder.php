<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1) 先に基礎データを投入
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
        ]);

        // 2) UserSeeder で作ったユーザーを取得（なければ作る）
        $users = User::all();
        if ($users->isEmpty()) {
            $users = User::factory()->count(5)->create();
        }

        // 3) ItemSeeder に users を渡して実行
        $this->callWith(ItemSeeder::class, ['users' => $users]);
    }
}
