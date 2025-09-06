<?php

namespace Database\Factories;

use App\Models\Ingredient;
use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Recipe ' . fake()->unique()->numberBetween(1, 1000),
            'content' => fake()->paragraph(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Recipe $recipe) {
            // Pobierz losowe składniki (np. 2-5)
            $ingredients = Ingredient::inRandomOrder()->take(fake()->numberBetween(2, 5))->get();

            foreach ($ingredients as $ingredient) {
                $recipe->ingredients()->attach(
                    $ingredient->id,
                    ['quantity' => fake()->numberBetween(1, 10)]
                );
            }
        });
    }
}
