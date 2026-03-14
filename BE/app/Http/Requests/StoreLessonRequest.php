<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('sanctum')->check() && auth('sanctum')->user()->isTeacher();
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'tieu_de' => $this->title ?? $this->tieu_de,
            'mo_ta' => $this->description ?? $this->mo_ta,
            'noi_dung' => $this->content ?? $this->noi_dung,
            'trang_thai' => $this->status ?? $this->trang_thai,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'tieu_de' => ['required', 'string', 'max:255'],
            'mo_ta' => ['nullable', 'string', 'max:1000'],
            'noi_dung' => ['required', 'string'],
            'trang_thai' => ['required', 'integer', 'in:1,2'], // 1: draft, 2: published
        ];
    }

    public function messages(): array
    {
        return [
            'tieu_de.required' => 'Tiêu đề là bắt buộc',
            'tieu_de.string' => 'Tiêu đề phải là chuỗi ký tự',
            'tieu_de.max' => 'Tiêu đề không được vượt quá 255 ký tự',
            'mo_ta.string' => 'Mô tả phải là chuỗi ký tự',
            'mo_ta.max' => 'Mô tả không được vượt quá 1000 ký tự',
            'noi_dung.required' => 'Nội dung là bắt buộc',
            'noi_dung.string' => 'Nội dung phải là chuỗi ký tự',
            'trang_thai.required' => 'Trạng thái là bắt buộc',
            'trang_thai.integer' => 'Trạng thái phải là một số',
            'trang_thai.in' => 'Trạng thái không hợp lệ',
        ];
    }
}
