<?php

namespace App\Observers;

use App\Models\StudentAnswerDetail;
use App\Models\CauHoi;

class StudentAnswerDetailObserver
{
    /**
     * Tự động chấm điểm từng câu trắc nghiệm khi tạo/cập nhật.
     * Việc tính tổng điểm được xử lý tập trung trong submitTest().
     */
    public function creating(StudentAnswerDetail $detail): void
    {
        $this->evaluateAnswer($detail);
    }

    public function updating(StudentAnswerDetail $detail): void
    {
        $this->evaluateAnswer($detail);
    }

    private function evaluateAnswer(StudentAnswerDetail $detail): void
    {
        $question = CauHoi::find($detail->id_cau_hoi);
        if (!$question) {
            return;
        }

        // Default
        $detail->la_dung = null;
        $detail->diem_cau_hoi = null;

        // Chấm tự động câu trắc nghiệm
        if ($question->isSingleChoice() || $question->isMultipleChoice()) {
            if ($detail->id_dap_an) {
                $answer = $question->dapAns()->find($detail->id_dap_an);
                $isCorrect = $answer ? $answer->isCorrect() : false;
                $detail->la_dung = $isCorrect;
                $detail->diem_cau_hoi = $isCorrect ? $question->diem_max : 0;
            } else {
                $detail->la_dung = false;
                $detail->diem_cau_hoi = 0;
            }
        }
        // Tự luận giữ null — chờ giáo viên chấm
    }
}
