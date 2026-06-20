<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class OnboardingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'role' => ['required', 'in:client,seller,driver'],
            'avatar' => ['nullable', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'interests' => ['required', 'array', 'min:3'],
            'interests.*' => ['string', 'max:50'],
        ];
    }
}