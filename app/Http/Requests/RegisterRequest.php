<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Prevent authenticated users from logging in.
     */
    public function authorize(): bool
    {
        return !Auth::hasUser();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
//            'username' => 'required|string',
            'token' => 'required|string',
            'password' => 'required|string',
        ];
    }


}
