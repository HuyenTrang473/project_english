<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDapAnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('sanctum')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'content' => 'required|string|min:1|max:1000',
            'isCorrect' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'content.required' => 'Nội dung đáp án không được trống',
            'content.min' => 'Nội dung đáp án phải có ít nhất 1 ký tự',
            'content.max' => 'Nội dung đáp án không được vượt quá 1000 ký tự',
            'isCorrect.boolean' => 'isCorrect phải là giá trị boolean',
        ];
    }
}
