<?php

namespace App\Http\Controllers;

use App\Models\CauHoi;
use App\Models\BaiTest;
use App\Http\Requests\StoreCauHoiRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CauHoiController extends Controller
{
    /**
     * Lấy danh sách câu hỏi theo bài test (giáo viên)
     */
    public function indexByTest($testId)
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
                    'message' => 'Bạn không có quyền xem câu hỏi của bài test này',
                ], 403);
            }

            $questions = CauHoi::where('id_bai_test', $testId)
                ->with('dapAns')
                ->orderBy('thu_tu')
                ->get()
                ->map(function ($q) {
                    return [
                        'id' => $q->id,
                        'noi_dung' => $q->noi_dung,
                        'mo_ta_chi_tiet' => $q->mo_ta_chi_tiet,
                        'loai_cau_hoi' => $q->loai_cau_hoi,
                        'hinh_anh_url' => $q->hinh_anh_url,
                        'ghi_chu' => $q->ghi_chu,
                        'thu_tu' => $q->thu_tu,
                        'diem_max' => $q->diem_max,
                        'dap_ans' => $q->dapAns->map(function ($a) {
                            return [
                                'id' => $a->id,
                                'noi_dung' => $a->noi_dung,
                                'hinh_anh_url' => $a->hinh_anh_url,
                                'mo_ta_chi_tiet' => $a->mo_ta_chi_tiet,
                                'la_dap_an_dung' => $a->la_dap_an_dung,
                                'diem_tu_dong' => $a->diem_tu_dong,
                                'thu_tu' => $a->thu_tu,
                            ];
                        }),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $questions,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách câu hỏi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tạo câu hỏi mới (giáo viên)
     */
    public function store(StoreCauHoiRequest $request, $testId)
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
                    'message' => 'Bạn không có quyền thêm câu hỏi vào bài test này',
                ], 403);
            }

            // Lấy thứ tự mới
            $maxOrder = CauHoi::where('id_bai_test', $testId)->max('thu_tu') ?? 0;

            $question = CauHoi::create([
                'id_bai_test' => $testId,
                'noi_dung' => $request->noi_dung ?? $request->content,
                'mo_ta_chi_tiet' => $request->mo_ta_chi_tiet,
                'loai_cau_hoi' => $request->loai_cau_hoi ?? $request->type ?? 'multiple_choice',
                'hinh_anh_url' => $request->hinh_anh_url,
                'ghi_chu' => $request->ghi_chu,
                'thu_tu' => $maxOrder + 1,
                'diem_max' => $request->diem_max ?? $request->maxScore ?? 1,
            ]);

            // Handle audio upload if present
            if ($request->hasFile('audio_file')) {
                try {
                    $this->handleQuestionAudioUpload($request, $question);
                } catch (\Exception $e) {
                    // Clean up the question if audio upload fails for listening questions
                    if ($request->loai_cau_hoi === 'listening') {
                        $question->delete();
                        return response()->json([
                            'success' => false,
                            'message' => 'Lỗi khi tải audio: ' . $e->getMessage(),
                        ], 422);
                    }
                    // For non-listening questions, log but continue
                    Log::warning('Audio upload failed for non-listening question: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Tạo câu hỏi thành công',
                'data' => [
                    'id' => $question->id,
                    'noi_dung' => $question->noi_dung,
                    'mo_ta_chi_tiet' => $question->mo_ta_chi_tiet,
                    'loai_cau_hoi' => $question->loai_cau_hoi,
                    'hinh_anh_url' => $question->hinh_anh_url,
                    'ghi_chu' => $question->ghi_chu,
                    'thu_tu' => $question->thu_tu,
                    'diem_max' => $question->diem_max,
                    'audio_url' => $question->audio_url,
                    'audio_file_name' => $question->audio_file_name,
                    'audio_file_size' => $question->audio_file_size,
                    'dap_ans' => [],
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo câu hỏi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cập nhật câu hỏi (giáo viên)
     */
    public function update(StoreCauHoiRequest $request, $testId, $questionId)
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
                    'message' => 'Bạn không có quyền sửa câu hỏi của bài test này',
                ], 403);
            }

            $question = CauHoi::where('id_bai_test', $testId)->find($questionId);

            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Câu hỏi không tồn tại',
                ], 404);
            }

            $question->update([
                'noi_dung' => $request->noi_dung ?? $request->content ?? $question->noi_dung,
                'mo_ta_chi_tiet' => $request->mo_ta_chi_tiet ?? $question->mo_ta_chi_tiet,
                'loai_cau_hoi' => $request->loai_cau_hoi ?? $request->type ?? $question->loai_cau_hoi,
                'hinh_anh_url' => $request->hinh_anh_url ?? $question->hinh_anh_url,
                'ghi_chu' => $request->ghi_chu ?? $question->ghi_chu,
                'diem_max' => $request->diem_max ?? $request->maxScore ?? $question->diem_max,
            ]);

            // Handle audio upload if present
            if ($request->hasFile('audio_file')) {
                try {
                    $this->handleQuestionAudioUpload($request, $question);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Lỗi khi tải audio: ' . $e->getMessage(),
                    ], 422);
                }
            }
            // Handle audio removal if flagged
            elseif ($request->input('_removeAudio') === true || $request->input('_removeAudio') === 'true') {
                $this->removeQuestionAudio($question);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật câu hỏi thành công',
                'data' => [
                    'id' => $question->id,
                    'noi_dung' => $question->noi_dung,
                    'mo_ta_chi_tiet' => $question->mo_ta_chi_tiet,
                    'loai_cau_hoi' => $question->loai_cau_hoi,
                    'hinh_anh_url' => $question->hinh_anh_url,
                    'ghi_chu' => $question->ghi_chu,
                    'thu_tu' => $question->thu_tu,
                    'diem_max' => $question->diem_max,
                    'audio_url' => $question->audio_url,
                    'audio_file_name' => $question->audio_file_name,
                    'audio_file_size' => $question->audio_file_size,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật câu hỏi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Xóa câu hỏi (giáo viên)
     */
    public function destroy($testId, $questionId)
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
                    'message' => 'Bạn không có quyền xóa câu hỏi của bài test này',
                ], 403);
            }

            $question = CauHoi::where('id_bai_test', $testId)->find($questionId);

            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Câu hỏi không tồn tại',
                ], 404);
            }

            $question->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa câu hỏi thành công',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa câu hỏi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle question audio file upload
     * Supports: MP3, WAV, OGG formats
     */
    private function handleQuestionAudioUpload($request, $question)
    {
        try {
            if (!$request->hasFile('audio_file')) {
                return;
            }

            $file = $request->file('audio_file');

            // Validate file size
            if ($file->getSize() > 50 * 1024 * 1024) { // 50MB max
                throw new \Exception('Dung lượng file không được vượt quá 50MB');
            }

            // Validate file format - support mp3, wav, ogg
            $validMimes = ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/x-wav'];
            $validExtensions = ['mp3', 'wav', 'ogg'];
            $extension = $file->getClientOriginalExtension();
            $mimeType = $file->getMimeType();

            if (!in_array($mimeType, $validMimes) && !in_array($extension, $validExtensions)) {
                throw new \Exception('Chỉ chấp nhận file audio MP3, WAV hoặc OGG');
            }

            // Remove old audio file if exists
            if ($question->audio_file_name && Storage::disk('public')->exists('audio/questions/' . $question->audio_file_name)) {
                Storage::disk('public')->delete('audio/questions/' . $question->audio_file_name);
            }

            // Store new audio file with proper naming
            $filename = 'question_' . $question->id . '_' . time() . '.' . $extension;
            $path = $file->storeAs('audio/questions', $filename, 'public');

            if (!$path) {
                throw new \Exception('Lỗi khi lưu file audio');
            }

            // Update question with audio info
            $question->update([
                'audio_file_name' => $filename,
                'audio_file_size' => $file->getSize(),
                'audio_url' => Storage::url($path),
            ]);

            Log::info("Audio uploaded for question {$question->id}: {$filename}");
        } catch (\Exception $e) {
            Log::error('Error uploading question audio: ' . $e->getMessage());
            throw $e; // Re-throw to be caught by controller
        }
    }

    /**
     * Remove audio file from question
     */
    private function removeQuestionAudio($question)
    {
        try {
            if ($question->audio_file_name && Storage::disk('public')->exists('audio/questions/' . $question->audio_file_name)) {
                Storage::disk('public')->delete('audio/questions/' . $question->audio_file_name);
            }

            $question->update([
                'audio_file_name' => null,
                'audio_file_size' => null,
                'audio_url' => null,
            ]);

            Log::info("Audio removed for question {$question->id}");
        } catch (\Exception $e) {
            Log::error('Error removing question audio: ' . $e->getMessage());
        }
    }
}
