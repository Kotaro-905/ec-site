<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemCategoriesTable extends Migration
{
    public function up(): void
    {
        Schema::create('item_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id')->nullable(false);
            $table->unsignedBigInteger('category_id')->nullable(false);
            $table->timestamps();

            $table->foreign('item_id')
                ->references('id')->on('items')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('category_id')
                ->references('id')->on('categories')
                ->cascadeOnUpdate()->cascadeOnDelete();

            // 検索用インデックスのみ（仕様：UNIQUEなし）
            $table->index(['item_id', 'category_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('item_categories');
    }
}

