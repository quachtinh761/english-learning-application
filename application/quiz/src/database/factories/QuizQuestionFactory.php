<?php

namespace Database\Factories;

use App\Enums\QuizQuestionTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class QuizQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = QuizQuestionTypeEnum::cases();
        return [
            'quiz_id' => $this->faker->randomDigitNot(0),
            'point' => random_int(1, 10),
            'question' => $this->faker->sentence(),
            // 'type' => $types[array_rand($types)]->value,
            'type' => QuizQuestionTypeEnum::SingleChoice->value, // temporarily set to single choice
            'options' => [
                'A' => $this->faker->sentence(),
                'B' => $this->faker->sentence(),
                'C' => $this->faker->sentence(),
                'D' => $this->faker->sentence(),
            ],
            'answer' => ['A', 'B', 'C', 'D'][array_rand(['A', 'B', 'C', 'D'])],
        ];
    }
}
