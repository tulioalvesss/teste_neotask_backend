<?php

namespace Database\Factories;

use App\Models\SuggestionSong;
use Illuminate\Database\Eloquent\Factories\Factory;

class SuggestionSongFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SuggestionSong::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'viewCount' => $this->faker->numberBetween(1000, 1000000),
            'link' => $this->faker->url(),
            'image' => $this->faker->imageUrl(),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected'])
        ];
    }
} 