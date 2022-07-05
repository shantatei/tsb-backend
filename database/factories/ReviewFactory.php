<?php

namespace Database\Factories;

use App\Models\reviews;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ReviewFactory extends Factory
{

    protected $model = reviews::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'username'=>$this->faker->sentence,
            'review'=>$this->faker->paragraph,
            'created_by'=>rand(1,10)
        ];
    }
}
