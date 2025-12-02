<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Detect if updating or creating
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],

            // Unique email (but ignore current user on update)
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $userId
            ],

            // Only required on create, optional on update
            'password' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'string',
                'min:6'
            ],
        ];
    }
}
