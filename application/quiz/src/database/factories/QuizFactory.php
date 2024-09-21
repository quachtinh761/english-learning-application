<?php

namespace Database\Factories;

use App\Enums\QuizStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class QuizFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = QuizStatusEnum::cases();

        return [
            'code' => $this->faker->unique()->slug(3),
            'quiz_category_id' => $this->faker->randomDigitNot(0),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->sentence(),
            'instruction' => $this->faker->sentence(),
            'start_time' => null,
            'end_time' => null,
            'duration_in_minutes' => 0,
            'number_of_questions' => $this->faker->randomDigitNot(0),
            'maximum_point' => $this->faker->randomDigitNot(0),
            'is_anonymous' => $this->faker->boolean(),
            'is_randomize_question' => $this->faker->boolean(),
            'number_of_submissions' => $this->faker->randomDigitNot(0),
            'lowest_point' => 0,
            'highest_point' => 0,
            'status' => $statuses[array_rand($statuses)]->value,
        ];
    }
}
