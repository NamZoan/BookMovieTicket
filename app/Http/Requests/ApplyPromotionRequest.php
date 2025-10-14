<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplyPromotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'promotion_code' => ['required','string','max:20'],
            'subtotal' => ['required','numeric','min:0'],
        ];
    }
}