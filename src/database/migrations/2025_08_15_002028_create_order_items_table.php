<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->unsignedBigInteger('item_id')->nullable(false);
            $table->integer('payment_method')->nullable(false); // 仕様: int
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('item_id')
                  ->references('id')->on('items')
                  ->cascadeOnUpdate()->restrictOnDelete();

            $table->index('user_id');
            $table->index('item_id');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
