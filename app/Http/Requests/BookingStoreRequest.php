<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'showtime_id' => ['required','integer','exists:showtimes,showtime_id'],
            'seats' => ['required','array','min:1'],
            'seats.*.seat_id' => ['required','integer','exists:seats,seat_id'],
            'seats.*.seat_type' => ['required','in:Normal,VIP,Couple'],
            'foods' => ['nullable','array'],
            'foods.*.item_id' => ['required_with:foods','integer','exists:food_items,item_id'],
            'foods.*.quantity' => ['required_with:foods','integer','min:1'],
            'promotion_code' => ['nullable','string','max:20'],
            'payment_method' => ['required','in:Cash,Credit Card,Banking,E-Wallet,Loyalty Points'],
            'agree' => ['accepted'],
        ];
    }
}