<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->index();
            $table->unsignedBigInteger('quiz_category_id')->index();
            $table->foreign('quiz_category_id')->references('id')->on('quiz_categories')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instruction')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->integer('duration_in_minutes')->default(0); // 0 means no time limit
            $table->unsignedInteger('number_of_questions');
            $table->unsignedInteger('maximum_point');
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_randomize_question')->default(false);
            $table->string('status')->default('draft')->index();
            $table->unsignedInteger('number_of_submissions')->default(0);
            $table->unsignedInteger('lowest_point')->default(0);
            $table->unsignedInteger('highest_point')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
