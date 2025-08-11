<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();      // 出品者
            $t->foreignId('category_id')->constrained()->restrictOnDelete(); // カテゴリ
            $t->string('name', 100);
            $t->string('brand', 100)->nullable();
            $t->text('description');
            $t->integer('price');
            $t->string('image', 255);

            // 商品の状態
            $t->tinyInteger('condition')
                ->comment('1=良好,2=目立った傷や汚れなし,3=やや傷や汚れあり,4=状態が悪い');

            // 出品状態
            $t->tinyInteger('status')->default(1)
                ->comment('0=下書き,1=公開,2=売却済');

            $t->timestamps();

            $t->index(['user_id', 'status']);
            $t->index(['category_id', 'condition']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};

