<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $userId = Auth::id();

        return [
            'display_name' => 'nullable|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($userId),
            ],
            'current_password' => 'nullable|required_with:new_password|string|size:64',
            'new_password' => 'nullable|required_with:current_password|string|size:64|different:current_password',
        ];
    }

    public function messages(): array
    {
        return [
            'username.unique' => __('profile.validation.username_unique'),
            'username.required' => __('profile.validation.username_required'),
            'display_name.max' => __('profile.validation.display_name_max'),
            'current_password.required_with' => __('profile.validation.current_password_required_with'),
            'new_password.required_with' => __('profile.validation.new_password_required_with'),
            'new_password.different' => __('profile.validation.new_password_different'),
            'current_password.size' => __('profile.validation.current_password_size'),
            'new_password.size' => __('profile.validation.new_password_size'),
        ];
    }
}
