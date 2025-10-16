<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
       return [
        'user_id'     => User::factory(),
        'category_id' => Category::factory(),
        'name'        => $this->faker->word,
        'brand'       => $this->faker->company, // ← ここが必須
        'description' => $this->faker->sentence,
        'price'       => $this->faker->numberBetween(100, 10000),
        'condition'   => 3,
        'image'       => null,
        'status'      => 1,
    ];
    }
}