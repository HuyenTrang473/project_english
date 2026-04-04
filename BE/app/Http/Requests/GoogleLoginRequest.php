<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoogleLoginRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'credential' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'credential.required' => 'Google credential la bat buoc.',
            'credential.string' => 'Google credential khong hop le.',
        ];
    }
}
