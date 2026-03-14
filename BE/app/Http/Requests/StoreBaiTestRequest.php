<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBaiTestRequest extends FormRequest
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
            'id_lesson' => $this->lessonId ?? $this->id_lesson,
            'ten_bai_test' => $this->testName ?? $this->ten_bai_test,
            'mo_ta' => $this->description ?? $this->mo_ta,
            'thoi_gian_toi_da' => $this->maxTime ?? $this->thoi_gian_toi_da,
            'diem_tong_max' => $this->maxScore ?? $this->diem_tong_max,
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
            'id_lesson' => ['required', 'integer', 'exists:lessons,id'],
            'ten_bai_test' => ['required', 'string', 'max:255'],
            'mo_ta' => ['nullable', 'string', 'max:1000'],
            'thoi_gian_toi_da' => ['required', 'integer', 'min:1', 'max:1440'], // min 1 phút, max 24 giờ
            'diem_tong_max' => ['required', 'numeric', 'min:0.01', 'max:10000'],
            'trang_thai' => ['required', 'integer', 'in:1,2'], // 1: draft, 2: published
        ];
    }

    public function messages(): array
    {
        return [
            'id_lesson.required' => 'Lesson là bắt buộc',
            'id_lesson.integer' => 'Lesson phải là một số',
            'id_lesson.exists' => 'Lesson không tồn tại',
            'ten_bai_test.required' => 'Tên bài test là bắt buộc',
            'ten_bai_test.string' => 'Tên bài test phải là chuỗi ký tự',
            'ten_bai_test.max' => 'Tên bài test không được vượt quá 255 ký tự',
            'mo_ta.string' => 'Mô tả phải là chuỗi ký tự',
            'mo_ta.max' => 'Mô tả không được vượt quá 1000 ký tự',
            'thoi_gian_toi_da.required' => 'Thời gian tối đa là bắt buộc',
            'thoi_gian_toi_da.integer' => 'Thời gian tối đa phải là một số',
            'thoi_gian_toi_da.min' => 'Thời gian tối đa phải ít nhất 1 phút',
            'thoi_gian_toi_da.max' => 'Thời gian tối đa không được vượt quá 1440 phút',
            'diem_tong_max.required' => 'Điểm tối đa là bắt buộc',
            'diem_tong_max.numeric' => 'Điểm tối đa phải là một số',
            'diem_tong_max.min' => 'Điểm tối đa phải lớn hơn 0',
            'diem_tong_max.max' => 'Điểm tối đa không được vượt quá 10000',
            'trang_thai.required' => 'Trạng thái là bắt buộc',
            'trang_thai.integer' => 'Trạng thái phải là một số',
            'trang_thai.in' => 'Trạng thái không hợp lệ',
        ];
    }
}
