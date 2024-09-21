<?php

namespace App\Http\Requests\Quiz;

use Illuminate\Foundation\Http\FormRequest;

class PostSubmitQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'string',
            'category_id' => 'integer',
            'keyword' => 'string',
            'page' => 'integer',
            'per_page' => 'integer',
        ];
    }
}
