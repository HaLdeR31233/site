<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Property::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['apartment', 'house', 'commercial'];
        $statuses = ['active', 'sold', 'rented'];
        
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(3),
            'price' => $this->faker->randomFloat(2, 500000, 5000000),
            'address' => $this->faker->address(),
            'rooms' => $this->faker->numberBetween(1, 5),
            'area' => $this->faker->randomFloat(2, 30, 200),
            'type' => $this->faker->randomElement($types),
            'status' => $this->faker->randomElement($statuses),
        ];
    }
}

