<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->id === (int) $this->route('user');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        if ($this->has('update_info')) {
            return [
                'name' => ['required', 'string', 'min:3', 'max:255'],
                'address' => ['required', 'string', 'max:255'],
                'grade' => ['required', 'integer', 'between:0,14'],
                'personal_info' => ['nullable', 'string', 'max:50000'],
                'working_place' => ['nullable', 'string', 'max:255'],
                'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            ];
        }

        if ($this->has('update_password')) {
            return [
                'old_password' => ['required', 'current_password'],
                'new_password' => ['required', 'string', Password::min(8)->letters()->numbers()],
                'password_confirmation' => ['required', 'same:new_password'],
            ];
        }

        return [];
    }
}
