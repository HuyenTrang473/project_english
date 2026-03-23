<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCauHoiRequest extends FormRequest
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
            'noi_dung' => 'required|string|min:5|max:2000',
            'loai_cau_hoi' => 'required|string|in:multiple_choice,true_false,essay,matching,fill_blank,image_choice',
            'mo_ta_chi_tiet' => 'nullable|string|max:1000',
            'ghi_chu' => 'nullable|string|max:1000',
            'hinh_anh_url' => 'nullable|url', // Optional now
            'diem_max' => 'nullable|numeric|min:0.5|max:100', // Optional now
            'audio_file' => 'nullable|file|mimes:mp3|max:51200', // max 50MB for audio
            // Legacy support
            'content' => 'nullable|string|min:5|max:2000',
            'type' => 'nullable|integer|in:1,2,3',
            'maxScore' => 'nullable|numeric|min:0.5|max:100',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'noi_dung.required' => 'Nội dung câu hỏi không được trống',
            'noi_dung.min' => 'Nội dung câu hỏi phải có ít nhất 5 ký tự',
            'noi_dung.max' => 'Nội dung câu hỏi không được vượt quá 2000 ký tự',
            'loai_cau_hoi.required' => 'Loại câu hỏi không được trống',
            'loai_cau_hoi.in' => 'Loại câu hỏi không hợp lệ',
            'hinh_anh_url.url' => 'Hình ảnh URL không hợp lệ',
            'diem_max.numeric' => 'Điểm tối đa phải là số',
            'diem_max.min' => 'Điểm tối đa phải lớn hơn 0.5',
            'diem_max.max' => 'Điểm tối đa không được vượt quá 100',
            // Legacy
            'content.required' => 'Nội dung câu hỏi không được trống',
            'content.min' => 'Nội dung câu hỏi phải có ít nhất 5 ký tự',
            'content.max' => 'Nội dung câu hỏi không được vượt quá 2000 ký tự',
            'type.required' => 'Loại câu hỏi không được trống',
            'type.in' => 'Loại câu hỏi không hợp lệ',
            'maxScore.numeric' => 'Điểm tối đa phải là số',
            'maxScore.min' => 'Điểm tối đa phải lớn hơn 0.5',
            'maxScore.max' => 'Điểm tối đa không được vượt quá 100',
        ];
    }
}
