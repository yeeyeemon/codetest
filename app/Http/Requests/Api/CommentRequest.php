<?php

namespace App\Http\Requests\Api;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'movie_id' => 'required|exists:movies,id',
            'parent_id' => 'nullable',
            'comment' => 'required|string'
        ];
    }

    
}
