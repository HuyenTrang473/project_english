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
        $user = auth('sanctum')->user();
        return auth('sanctum')->check() && $user && ($user->isTeacher() || $user->isAdmin());
    }

    protected function prepareForValidation()
    {
        // Log incoming data before any processing
        $allData = $this->all();
        \Log::info('StoreLessonRequest BEFORE processing:', [
            'all_data_keys' => array_keys($allData),
            'tieu_de' => $this->input('tieu_de'),
            'noi_dung' => $this->input('noi_dung'),
            'trang_thai' => $this->input('trang_thai'),
            'mo_ta' => $this->input('mo_ta'),
            'has_file' => $this->hasFile('file'),
        ]);

        $merge = [];

        // Cast trang_thai to integer for proper validation
        if ($this->has('trang_thai')) {
            $trang_thai = $this->input('trang_thai');
            $merge['trang_thai'] = is_numeric($trang_thai) ? (int) $trang_thai : null;
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }

        // Log after merge
        \Log::info('StoreLessonRequest AFTER merge:', [
            'tieu_de' => $this->input('tieu_de'),
            'noi_dung' => $this->input('noi_dung'),
            'trang_thai' => $this->input('trang_thai'),
            'mo_ta' => $this->input('mo_ta'),
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
            'tieu_de' => ['required', 'string', 'max:255', 'filled'],
            'mo_ta' => ['nullable', 'string', 'max:1000'],
            'noi_dung' => ['required', 'string', 'min:1', 'filled'],
            'trang_thai' => ['required', 'integer', 'in:1,2'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,txt', 'max:10240'],
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
            'noi_dung.min' => 'Nội dung không được để trống',
            'trang_thai.required' => 'Trạng thái là bắt buộc',
            'trang_thai.integer' => 'Trạng thái phải là một số',
            'trang_thai.in' => 'Trạng thái không hợp lệ',
            'file.file' => 'Tệp upload không hợp lệ',
            'file.mimes' => 'Định dạng file phải là PDF, DOC, DOCX hoặc TXT',
            'file.max' => 'File không được vượt quá 10MB',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Log validation errors for debugging
        \Log::error('Validation failed:', $validator->errors()->toArray());
        throw new \Illuminate\Validation\ValidationException($validator);
    }
}
