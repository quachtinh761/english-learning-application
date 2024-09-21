<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizCategory;
use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        QuizCategory::factory()->count(10)->create([
            'number_of_quizzes' => random_int(10, 20),
        ]);
        $categories = QuizCategory::all();

        foreach ($categories as $category) {
            Quiz::factory()->count($category->number_of_quizzes)->create([
                'quiz_category_id' => $category->id,
                'number_of_questions' => random_int(5, 10),
            ])->each(function ($quiz) {
                QuizQuestion::factory()->count($quiz->number_of_questions)->create([
                    'quiz_id' => $quiz->id,
                ]);

                $numberOfSubmissions = random_int(10, 20);
                $quiz->update([
                    'maximum_point' => $quiz->quizQuestions->sum('point'),
                    'number_of_submissions' => $numberOfSubmissions,
                ]);

                // Add some submissions
                for ($i = 0; $i < $numberOfSubmissions; $i++) {
                    $totalPoints = random_int(0, $quiz->maximum_point);
                    $quiz->quizSubmissions()->create([
                        'submission_code' => 'SUB' . str_pad($i, 5, '0', STR_PAD_LEFT),
                        'user_id' => random_int(1, 100),
                        'user_name' => 'User ' . $i,
                        'user_email' => 'user' . $i . '@example.com',
                        'number_of_corrections' => random_int(0, $quiz->number_of_questions),
                        'total_points' => $totalPoints,
                        'detail' => $quiz->quizQuestions->map(function ($question) use ($totalPoints) {
                            $point = random_int(0, $question->point);
                            return [
                                'question_id' => $question->id,
                                'point' => $point,
                                'is_correct' => $point === $question->point,
                            ];
                        })->toArray(),
                    ]);
                }

            });
        }
    }
}
