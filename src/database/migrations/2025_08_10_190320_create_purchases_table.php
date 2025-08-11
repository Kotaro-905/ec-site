<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();    // 購入者
            $t->foreignId('item_id')->constrained()->restrictOnDelete();   // 購入商品
            $t->integer('price_at_purchase');                              // 購入時価格
            $t->tinyInteger('status')->default(0)
                ->comment('0=注文,1=支払済,2=発送済,3=受取済,4=キャンセル');
            $t->timestamps();

            $t->index(['user_id', 'status']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};

