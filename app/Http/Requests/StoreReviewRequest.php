<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Authorization is handled by the ReviewPolicy, which will be called in the controller.
        // Returning true here allows the validation to proceed.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $movieId = $this->route('movie')->movie_id;

        return [
            'rating' => 'required|integer|min:1|max:10',
            'comment' => 'nullable|string|max:1000',
            'user_id' => Rule::unique('reviews')->where(function ($query) use ($movieId) {
                return $query->where('movie_id', $movieId);
            }),
        ];
    }

    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'rating.required' => 'Vui lòng cung cấp điểm đánh giá.',
            'rating.integer' => 'Điểm đánh giá phải là một số nguyên.',
            'rating.min' => 'Điểm đánh giá phải từ 1 đến 10.',
            'rating.max' => 'Điểm đánh giá phải từ 1 đến 10.',
            'comment.max' => 'Bình luận không được vượt quá 1000 ký tự.',
            'user_id.unique' => 'Bạn đã đánh giá phim này rồi.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => $this->user()->user_id,
        ]);
    }
}
