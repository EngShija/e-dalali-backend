<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && ($user->role === 'customer');
    }

    public function rules()
    {
        return [
            'listing_id' => 'required|integer|exists:listings,id',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d',
        ];
    }

    public function messages()
    {
        return [
            'listing_id.required' => 'Listing is required',
            'start_date.required' => 'Start date is required',
            'end_date.required' => 'End date is required',
        ];
    }
}
