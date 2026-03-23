<?php

namespace App\Http\Controllers;

use App\Models\DapAn;
use App\Models\CauHoi;
use App\Models\BaiTest;
use App\Http\Requests\StoreDapAnRequest;
use Illuminate\Http\Request;

class DapAnController extends Controller
{
    /**
     * Tạo đáp án mới (giáo viên)
     */
    public function store(StoreDapAnRequest $request, $testId, $questionId)
    {
        try {
            $test = BaiTest::find($testId);

            if (!$test) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài test không tồn tại',
                ], 404);
            }

            $currentUserId = auth('sanctum')->id();
            $currentUser = auth('sanctum')->user();
            $isAdmin = $currentUser && $currentUser->role === 'admin';

            if ((int)$test->id_giao_vien !== (int)$currentUserId && !$isAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền thêm đáp án',
                ], 403);
            }

            $question = CauHoi::where('id_bai_test', $testId)->find($questionId);

            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Câu hỏi không tồn tại',
                ], 404);
            }

            // Lấy thứ tự mới
            $maxOrder = DapAn::where('id_cau_hoi', $questionId)->max('thu_tu') ?? 0;

            $answer = DapAn::create([
                'id_cau_hoi' => $questionId,
                'noi_dung' => $request->noi_dung ?? $request->content,
                'hinh_anh_url' => $request->hinh_anh_url,
                'mo_ta_chi_tiet' => $request->mo_ta_chi_tiet,
                'la_dap_an_dung' => $request->la_dap_an_dung ?? $request->isCorrect ?? false,
                'diem_tu_dong' => $request->diem_tu_dong ?? 0,
                'thu_tu' => $maxOrder + 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo đáp án thành công',
                'data' => [
                    'id' => $answer->id,
                    'noi_dung' => $answer->noi_dung,
                    'hinh_anh_url' => $answer->hinh_anh_url,
                    'mo_ta_chi_tiet' => $answer->mo_ta_chi_tiet,
                    'la_dap_an_dung' => $answer->la_dap_an_dung,
                    'diem_tu_dong' => $answer->diem_tu_dong,
                    'thu_tu' => $answer->thu_tu,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo đáp án: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cập nhật đáp án (giáo viên)
     */
    public function update(StoreDapAnRequest $request, $testId, $questionId, $answerId)
    {
        try {
            $test = BaiTest::find($testId);

            if (!$test) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài test không tồn tại',
                ], 404);
            }

            $currentUserId = auth('sanctum')->id();
            $currentUser = auth('sanctum')->user();
            $isAdmin = $currentUser && $currentUser->role === 'admin';

            if ((int)$test->id_giao_vien !== (int)$currentUserId && !$isAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền sửa đáp án',
                ], 403);
            }

            $question = CauHoi::where('id_bai_test', $testId)->find($questionId);

            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Câu hỏi không tồn tại',
                ], 404);
            }

            $answer = DapAn::where('id_cau_hoi', $questionId)->find($answerId);

            if (!$answer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đáp án không tồn tại',
                ], 404);
            }

            $answer->update([
                'noi_dung' => $request->noi_dung ?? $request->content ?? $answer->noi_dung,
                'hinh_anh_url' => $request->hinh_anh_url ?? $answer->hinh_anh_url,
                'mo_ta_chi_tiet' => $request->mo_ta_chi_tiet ?? $answer->mo_ta_chi_tiet,
                'la_dap_an_dung' => $request->la_dap_an_dung ?? $request->isCorrect ?? $answer->la_dap_an_dung,
                'diem_tu_dong' => $request->diem_tu_dong ?? $answer->diem_tu_dong,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật đáp án thành công',
                'data' => [
                    'id' => $answer->id,
                    'noi_dung' => $answer->noi_dung,
                    'hinh_anh_url' => $answer->hinh_anh_url,
                    'mo_ta_chi_tiet' => $answer->mo_ta_chi_tiet,
                    'la_dap_an_dung' => $answer->la_dap_an_dung,
                    'diem_tu_dong' => $answer->diem_tu_dong,
                    'thu_tu' => $answer->thu_tu,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật đáp án: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Xóa đáp án (giáo viên)
     */
    public function destroy($testId, $questionId, $answerId)
    {
        try {
            $test = BaiTest::find($testId);

            if (!$test) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài test không tồn tại',
                ], 404);
            }

            $currentUserId = auth('sanctum')->id();
            $currentUser = auth('sanctum')->user();
            $isAdmin = $currentUser && $currentUser->role === 'admin';

            if ((int)$test->id_giao_vien !== (int)$currentUserId && !$isAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa đáp án',
                ], 403);
            }

            $question = CauHoi::where('id_bai_test', $testId)->find($questionId);

            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Câu hỏi không tồn tại',
                ], 404);
            }

            $answer = DapAn::where('id_cau_hoi', $questionId)->find($answerId);

            if (!$answer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đáp án không tồn tại',
                ], 404);
            }

            $answer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa đáp án thành công',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa đáp án: ' . $e->getMessage(),
            ], 500);
        }
    }
}
