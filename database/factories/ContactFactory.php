<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => fake()->numberBetween(1, 5),
            'first_name' => fake()->lastName(),
            'last_name' => fake()->firstName(),
            'gender' => fake()->numberBetween(1, 3),
            'email' => fake()->unique()->email(),
            'tel' => '090'.fake()->numerify('########'),
            'address' => fake()->address(),
            'building' => fake()->buildingNumber(),
            'detail' => fake()->realText(120),
        ];
    }

    /**
     * 性別を男にする
     */
    public function manGender(): static
    {
        return $this->state(fn (array $attribute) => [
            'gender' => 1,
        ]);
    }

    /**
     * 性別を女にする
     */
    public function womanGender(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 2,
        ]);
    }
}
