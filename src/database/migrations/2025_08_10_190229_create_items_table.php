<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable(false);
            $table->string('name', 100)->nullable(false);
            $table->text('description')->nullable(false);
            $table->integer('price')->nullable(false);
            $table->string('image', 255)->nullable(false);
            $table->string('brand', 100)->nullable(false);
            $table->integer('status')->nullable(false);    // 公開・下書き
            $table->integer('condition')->nullable(false); // 新品・良好…
            $table->timestamps();

            // 外部キー & インデックス
            $table->foreign('category_id')
                ->references('id')->on('categories')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->index('category_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};

