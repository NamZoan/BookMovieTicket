<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Authorization is handled in the policy
    }

    public function rules()
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:10'],
            'comment' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages()
    {
        return [
            'rating.required' => 'Vui lòng chọn số sao đánh giá.',
            'rating.min' => 'Đánh giá phải từ 1 sao trở lên.',
            'rating.max' => 'Đánh giá không được quá 10 sao.',
            'comment.required' => 'Vui lòng nhập nội dung đánh giá.',
            'comment.max' => 'Nội dung đánh giá không được vượt quá 1000 ký tự.'
        ];
    }
}