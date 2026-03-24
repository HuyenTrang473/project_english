<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitTestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('sanctum')->check();
    }

    protected function prepareForValidation()
    {
        if ($this->has('answers')) {
            $mappedAnswers = collect($this->answers)->map(function ($answer) {
                // Map question ID from various frontend formats
                $questionId = $answer['id_cau_hoi'] ?? $answer['questionId'] ?? $answer['cau_hoi_id'] ?? null;

                // Map answer ID: could be id_dap_an, answerId, or dap_an_hoc_sinh (if it's a numeric ID)
                $answerId = $answer['id_dap_an'] ?? $answer['answerId'] ?? null;
                $freeText = $answer['cau_tra_loi_tu_do'] ?? $answer['freeTextAnswer'] ?? null;

                // dap_an_hoc_sinh from TestTaker could be an answer ID (number) or free text (string)
                if (!$answerId && isset($answer['dap_an_hoc_sinh'])) {
                    $val = $answer['dap_an_hoc_sinh'];
                    if (is_numeric($val)) {
                        $answerId = (int) $val;
                    } else {
                        $freeText = $val;
                    }
                }

                return [
                    'id_cau_hoi' => $questionId ? (int) $questionId : null,
                    'id_dap_an' => $answerId ? (int) $answerId : null,
                    'cau_tra_loi_tu_do' => $freeText,
                ];
            })->toArray();

            $this->merge([
                'answers' => $mappedAnswers,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'answers' => ['required', 'array'],
            'answers.*.id_cau_hoi' => ['required', 'integer', 'exists:cau_hois,id'],
            'answers.*.id_dap_an' => ['nullable', 'integer', 'exists:dap_ans,id'],
            'answers.*.cau_tra_loi_tu_do' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'answers.required' => 'Danh sách câu trả lời là bắt buộc',
            'answers.array' => 'Câu trả lời phải là một mảng',
            'answers.*.id_cau_hoi.required' => 'ID câu hỏi là bắt buộc',
            'answers.*.id_cau_hoi.integer' => 'ID câu hỏi phải là một số',
            'answers.*.id_cau_hoi.exists' => 'Câu hỏi không tồn tại',
            'answers.*.id_dap_an.integer' => 'ID đáp án phải là một số',
            'answers.*.id_dap_an.exists' => 'Đáp án không tồn tại',
            'answers.*.cau_tra_loi_tu_do.string' => 'Câu trả lời tự do phải là chuỗi ký tự',
        ];
    }
}
