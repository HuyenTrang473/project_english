<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBaiTestRequest;
use App\Http\Requests\SubmitTestRequest;
use App\Models\BaiTest;
use App\Models\CauHoi;
use App\Models\DapAn;
use App\Models\CourseEnrollment;
use App\Models\StudentTestResult;
use App\Models\StudentAnswerDetail;
use App\Models\TestAnalytic;
use App\Models\QuestionAnalytic;
use App\Http\Resources\BaiTestResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BaiTestController extends Controller
{
    // Danh sách bài test của một bài học (public - với pagination & filter)

    public function indexByLesson($lessonId, Request $request)
    {
        try {
            $query = BaiTest::where('id_lesson', $lessonId)
                ->with('giaoVien:id,name');

            // Only show published tests by default, but allow including drafts if requested
            if (!$request->has('include_drafts') || !$request->include_drafts) {
                $query->published();
            }

            // Filter by name/search
            if ($request->has('search') && $request->search) {
                $query->where('ten_bai_test', 'like', '%' . $request->search . '%');
            }

            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('trang_thai', $request->status);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $baiTests = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => BaiTestResource::collection($baiTests->items()),
                'pagination' => [
                    'total' => $baiTests->total(),
                    'per_page' => $baiTests->perPage(),
                    'current_page' => $baiTests->currentPage(),
                    'last_page' => $baiTests->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách bài test: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Danh sách tất cả bài test có thể xem (authenticated users)
    public function getAllTests(Request $request)
    {
        try {
            $user = auth('sanctum')->user();

            // Nếu là giáo viên hoặc admin, xem bài test của họ (bất kỳ trạng thái nào)
            // Nếu là học sinh, chỉ xem bài test đã công bố
            if ($user && ($user->role === 'giao_vien' || $user->role === 'admin')) {
                $query = BaiTest::with('lesson:id,tieu_de')->with('giaoVien:id,name');
                if ($user && $user->role !== 'admin') {
                    $query->where('id_giao_vien', $user->id);
                }
            } else {
                // Học sinh chỉ xem bài test đã công bố
                $query = BaiTest::published()
                    ->with('giaoVien:id,name');
            }

            // Filter by name/search
            if ($request->has('search') && $request->search) {
                $query->where('ten_bai_test', 'like', '%' . $request->search . '%');
            }

            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('trang_thai', $request->status);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $tests = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => BaiTestResource::collection($tests->items()),
                'pagination' => [
                    'total' => $tests->total(),
                    'per_page' => $tests->perPage(),
                    'current_page' => $tests->currentPage(),
                    'last_page' => $tests->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách bài test: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Danh sách bài test của giáo viên (giáo viên - với pagination & filter)
    public function myTests(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $query = BaiTest::with('lesson:id,tieu_de');

            if ($user && $user->role !== 'admin') {
                $query->where('id_giao_vien', $user->id);
            }

            // Filter by name/search
            if ($request->has('search') && $request->search) {
                $query->where('ten_bai_test', 'like', '%' . $request->search . '%');
            }

            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('trang_thai', $request->status);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $tests = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => BaiTestResource::collection($tests->items()),
                'pagination' => [
                    'total' => $tests->total(),
                    'per_page' => $tests->perPage(),
                    'current_page' => $tests->currentPage(),
                    'last_page' => $tests->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách bài test: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Lấy chi tiết bài test (tất cả users đã auth - với shuffle nếu cần)
    public function show($id, Request $request)
    {
        try {
            $test = BaiTest::with('giaoVien:id,name')
                ->find($id);

            if (!$test) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài test không tồn tại',
                ], 404);
            }


            $user = auth('sanctum')->user();
            $isOwner = $test->id_giao_vien === auth('sanctum')->id();
            $isAdmin = $user && ($user->role === 'admin' || $user->role === 2);
            $isPublished = $test->trang_thai === 2;

            if (!$isPublished && !$isOwner && !$isAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xem bài test này',
                ], 403);
            }

            // Kiểm tra thời gian availability (chỉ cho non-owner)
            if (!$isOwner && !$this->isTestAvailable($test)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài test này không còn khả dụng',
                ], 403);
            }

            $questions = CauHoi::where('id_bai_test', $id)
                ->orderBy('thu_tu')
                ->get();

            // Shuffle câu hỏi nếu cài đặt
            if ($test->co_xao_tron_cau_hoi) {
                $questions = $questions->shuffle();
            }

            $data = [
                'id' => $test->id,
                'ten_bai_test' => $test->ten_bai_test,
                'loai_quiz' => $test->loai_quiz,
                'mo_ta' => $test->mo_ta,
                'thoi_gian_toi_da' => $test->thoi_gian_toi_da,
                'diem_tong_max' => $test->diem_tong_max,
                'giao_vien' => [
                    'id' => $test->giaoVien->id,
                    'name' => $test->giaoVien->name,
                ],
                'questions' => $questions->map(function ($q) use ($test) {
                    $answers = $q->dapAns;
                    // Shuffle đáp án nếu cài đặt
                    if ($test->co_xao_tron_dap_an) {
                        $answers = $answers->shuffle();
                    }

                    return [
                        'id' => $q->id,
                        'noi_dung' => $q->noi_dung,
                        'mo_ta_chi_tiet' => $q->mo_ta_chi_tiet,
                        'loai_cau_hoi' => $q->loai_cau_hoi,
                        'hinh_anh_url' => $q->hinh_anh_url,
                        'audio_url' => $this->normalizeQuestionAudioUrl($q->audio_url, request()),
                        'audio_file_name' => $q->audio_file_name,
                        'audio_file_size' => $q->audio_file_size,
                        'diem_max' => $q->diem_max,
                        'thu_tu' => $q->thu_tu,
                        'answers' => $answers->map(function ($a) {
                            return [
                                'id' => $a->id,
                                'noi_dung' => $a->noi_dung,
                                'hinh_anh_url' => $a->hinh_anh_url,
                                'thu_tu' => $a->thu_tu,
                            ];
                        })->values(),
                    ];
                })->values(),
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy chi tiết bài test: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy chi tiết bài test cho giáo viên (edit mode - với đáp án đúng)
     */
    public function showForTeacher($id)
    {
        try {
            $test = BaiTest::with(['lesson', 'giaoVien', 'cauHois.dapAns'])
                ->find($id);

            if (!$test) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài test không tồn tại',
                ], 404);
            }

            // Check authorization - only teacher who owns the test or admin can view it
            $currentUserId = auth('sanctum')->id();
            $currentUser = auth('sanctum')->user();
            $isAdmin = $currentUser && ($currentUser->role === 'admin' || $currentUser->role === 2);

            Log::info('ShowForTeacher Authorization Check', [
                'test_id' => $id,
                'test_giao_vien_id' => $test->id_giao_vien,
                'current_user_id' => $currentUserId,
                'current_user' => $currentUser ? [
                    'id' => $currentUser->id,
                    'role' => $currentUser->role,
                    'name' => $currentUser->name,
                ] : 'null',
                'is_admin' => $isAdmin,
                'comparison' => (int)$test->id_giao_vien . ' === ' . (int)$currentUserId,
            ]);

            if ((int)$test->id_giao_vien !== (int)$currentUserId && !$isAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xem bài test này - Debug: Test by ' . $test->id_giao_vien . ', You are ' . $currentUserId . ', Admin: ' . ($isAdmin ? 'yes' : 'no'),
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => new BaiTestResource($test),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy chi tiết bài test: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tạo bài test mới (giáo viên)
     */
    public function store(StoreBaiTestRequest $request)
    {
        try {
            DB::beginTransaction();

            // Log questions data received
            Log::info('Create BaiTest - Questions Data Received', [
                'has_questions' => $request->has('questions'),
                'questions_raw' => $request->get('questions'),
            ]);

            $test = BaiTest::create([
                'id_giao_vien' => auth('sanctum')->id(),
                'id_lesson' => $request->id_lesson,
                'ten_bai_test' => $request->ten_bai_test,
                'loai_quiz' => $request->loai_quiz ?? 'mixed',
                'chi_tiet_loai_quiz' => $request->chi_tiet_loai_quiz,
                'mo_ta' => $request->mo_ta,
                'thoi_gian_toi_da' => $request->thoi_gian_toi_da,
                'diem_tong_max' => $request->diem_tong_max,
                'trang_thai' => $request->trang_thai ?? 1,
                'so_lan_lam_toi_da' => in_array($request->input('so_lan_lam_toi_da'), ['null', 'undefined', ''], true) ? 1 : $request->input('so_lan_lam_toi_da', 1),
                'diem_dat' => in_array($request->input('diem_dat'), ['null', 'undefined', ''], true) ? null : $request->input('diem_dat', 0),
                'co_xao_tron_cau_hoi' => $request->boolean('co_xao_tron_cau_hoi', false),
                'co_xao_tron_dap_an' => $request->boolean('co_xao_tron_dap_an', false),
                'hien_thi_ket_qua_ngay_lap' => $request->boolean('hien_thi_ket_qua_ngay_lap', true),
                'hien_thi_dap_an_dung' => $request->boolean('hien_thi_dap_an_dung', true),
                'cho_xem_lai_test' => $request->boolean('cho_xem_lai_test', true),
                'ngay_bat_dau' => in_array($request->input('ngay_bat_dau'), ['null', 'undefined', ''], true) ? null : $request->input('ngay_bat_dau'),
                'ngay_ket_thuc' => in_array($request->input('ngay_ket_thuc'), ['null', 'undefined', ''], true) ? null : $request->input('ngay_ket_thuc'),
            ]);

            // Create questions if provided
            $questionsData = $this->parseQuestionsData($request);
            Log::info('Parsed Questions Data', ['count' => count($questionsData ?? []), 'data' => $questionsData]);

            if ($questionsData) {
                foreach ($questionsData as $index => $questionData) {
                    $question = CauHoi::create([
                        'id_bai_test' => $test->id,
                        'noi_dung' => $questionData['noi_dung'] ?? $questionData['content'] ?? '',
                        'mo_ta_chi_tiet' => $questionData['mo_ta_chi_tiet'] ?? '',
                        'loai_cau_hoi' => $questionData['loai_cau_hoi'] ?? $questionData['type'] ?? 'multiple_choice',
                        'hinh_anh_url' => $questionData['hinh_anh_url'] ?? null,
                        'ghi_chu' => $questionData['ghi_chu'] ?? null,
                        'thu_tu' => $index + 1,
                        'diem_max' => $questionData['diem_max'] ?? $questionData['diem_toi_da'] ?? 1,
                    ]);

                    // Handle audio upload if present
                    $audioKey = 'audio_' . $index;
                    if ($request->hasFile($audioKey)) {
                        $this->handleQuestionAudioUpload($request, $audioKey, $question);
                    }

                    // Create answers if provided
                    if (isset($questionData['answers']) && is_array($questionData['answers'])) {
                        foreach ($questionData['answers'] as $answerIndex => $answerData) {
                            DapAn::create([
                                'id_cau_hoi' => $question->id,
                                'noi_dung' => $answerData['noi_dung'] ?? $answerData['content'] ?? '',
                                'hinh_anh_url' => $answerData['hinh_anh_url'] ?? null,
                                'mo_ta_chi_tiet' => $answerData['mo_ta_chi_tiet'] ?? null,
                                'la_dap_an_dung' => $answerData['la_dap_an_dung'] ?? $answerData['correct'] ?? false,
                                'diem_tu_dong' => $answerData['diem_tu_dong'] ?? 0,
                                'thu_tu' => $answerIndex + 1,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tạo bài test thành công',
                'data' => new BaiTestResource($test),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo bài test: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cập nhật bài test (giáo viên)
     */
    public function update(StoreBaiTestRequest $request, $id)
    {
        try {
            $test = BaiTest::find($id);

            if (!$test) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài test không tồn tại',
                ], 404);
            }

            $currentUserId = auth('sanctum')->id();
            $currentUser = auth('sanctum')->user();
            $isAdmin = $currentUser && $currentUser->role === 'admin';

            Log::info('Update BaiTest Authorization Check', [
                'test_id' => $id,
                'test_giao_vien_id' => $test->id_giao_vien,
                'current_user_id' => $currentUserId,
                'current_user_role' => $currentUser ? $currentUser->role : 'null',
                'is_admin' => $isAdmin,
            ]);

            // Log questions data received
            Log::info('Update BaiTest - Questions Data Received', [
                'has_questions' => $request->has('questions'),
                'questions_raw' => $request->get('questions'),
            ]);

            if ((int)$test->id_giao_vien !== (int)$currentUserId && !$isAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền cập nhật bài test này - Debug: Test by ' . $test->id_giao_vien . ', You are ' . $currentUserId . ', Admin: ' . ($isAdmin ? 'yes' : 'no'),
                ], 403);
            }

            DB::beginTransaction();

            $test->update([
                'id_lesson' => $request->id_lesson,
                'ten_bai_test' => $request->ten_bai_test,
                'loai_quiz' => $request->loai_quiz ?? $test->loai_quiz,
                'chi_tiet_loai_quiz' => $request->chi_tiet_loai_quiz ?? $test->chi_tiet_loai_quiz,
                'mo_ta' => $request->mo_ta,
                'thoi_gian_toi_da' => $request->thoi_gian_toi_da,
                'diem_tong_max' => $request->diem_tong_max,
                'trang_thai' => $request->trang_thai,
                'so_lan_lam_toi_da' => !$request->has('so_lan_lam_toi_da') || in_array($request->input('so_lan_lam_toi_da'), ['null', 'undefined', ''], true) ? $test->so_lan_lam_toi_da : $request->input('so_lan_lam_toi_da'),
                'diem_dat' => !$request->has('diem_dat') || in_array($request->input('diem_dat'), ['null', 'undefined', ''], true) ? $test->diem_dat : $request->input('diem_dat'),
                'co_xao_tron_cau_hoi' => $request->has('co_xao_tron_cau_hoi') ? $request->boolean('co_xao_tron_cau_hoi') : $test->co_xao_tron_cau_hoi,
                'co_xao_tron_dap_an' => $request->has('co_xao_tron_dap_an') ? $request->boolean('co_xao_tron_dap_an') : $test->co_xao_tron_dap_an,
                'hien_thi_ket_qua_ngay_lap' => $request->has('hien_thi_ket_qua_ngay_lap') ? $request->boolean('hien_thi_ket_qua_ngay_lap') : $test->hien_thi_ket_qua_ngay_lap,
                'hien_thi_dap_an_dung' => $request->has('hien_thi_dap_an_dung') ? $request->boolean('hien_thi_dap_an_dung') : $test->hien_thi_dap_an_dung,
                'cho_xem_lai_test' => $request->has('cho_xem_lai_test') ? $request->boolean('cho_xem_lai_test') : $test->cho_xem_lai_test,
                'ngay_bat_dau' => in_array($request->input('ngay_bat_dau'), ['null', 'undefined', ''], true) ? null : ($request->has('ngay_bat_dau') ? $request->input('ngay_bat_dau') : $test->ngay_bat_dau),
                'ngay_ket_thuc' => in_array($request->input('ngay_ket_thuc'), ['null', 'undefined', ''], true) ? null : ($request->has('ngay_ket_thuc') ? $request->input('ngay_ket_thuc') : $test->ngay_ket_thuc),
            ]);

            // Handle questions update if provided
            $questionsData = $this->parseQuestionsData($request);
            if ($questionsData) {
                // Separate existing questions (with valid database id) from new questions (with temporary or no id)
                $existingQuestionIds = [];
                foreach ($questionsData as $index => $questionData) {
                    $questionId = $questionData['id'] ?? null;

                    // Check if this is an existing question in the database
                    $existingQuestion = null;
                    if ($questionId) {
                        $existingQuestion = CauHoi::where('id_bai_test', $test->id)->find($questionId);
                    }

                    if ($existingQuestion) {
                        // Update existing question
                        $existingQuestionIds[] = $questionId;
                        $existingQuestion->update([
                            'noi_dung' => $questionData['noi_dung'] ?? $existingQuestion->noi_dung,
                            'mo_ta_chi_tiet' => $questionData['mo_ta_chi_tiet'] ?? $existingQuestion->mo_ta_chi_tiet,
                            'loai_cau_hoi' => $questionData['loai_cau_hoi'] ?? $existingQuestion->loai_cau_hoi,
                            'hinh_anh_url' => $questionData['hinh_anh_url'] ?? $existingQuestion->hinh_anh_url,
                            'ghi_chu' => $questionData['ghi_chu'] ?? $existingQuestion->ghi_chu,
                            'thu_tu' => $index + 1,
                            'diem_max' => $questionData['diem_max'] ?? $questionData['diem_toi_da'] ?? $existingQuestion->diem_max,
                        ]);

                        // Update answers - delete old and create new
                        if (isset($questionData['answers']) && is_array($questionData['answers'])) {
                            // Delete old answers
                            $existingQuestion->dapAns()->delete();

                            // Create new answers
                            foreach ($questionData['answers'] as $answerIndex => $answerData) {
                                DapAn::create([
                                    'id_cau_hoi' => $existingQuestion->id,
                                    'noi_dung' => $answerData['noi_dung'] ?? $answerData['content'] ?? '',
                                    'hinh_anh_url' => $answerData['hinh_anh_url'] ?? null,
                                    'mo_ta_chi_tiet' => $answerData['mo_ta_chi_tiet'] ?? null,
                                    'la_dap_an_dung' => $answerData['la_dap_an_dung'] ?? $answerData['correct'] ?? false,
                                    'diem_tu_dong' => $answerData['diem_tu_dong'] ?? 0,
                                    'thu_tu' => $answerIndex + 1,
                                ]);
                            }
                        }

                        // Handle audio upload if present
                        $audioKey = 'audio_' . $index;
                        if ($request->hasFile($audioKey)) {
                            $this->handleQuestionAudioUpload($request, $audioKey, $existingQuestion);
                        } elseif ($questionData['_removeAudio'] ?? false) {
                            // Remove audio if flagged
                            $this->removeQuestionAudio($existingQuestion);
                        }
                    } else {
                        // Create new question (has no ID or temporary ID)
                        $question = CauHoi::create([
                            'id_bai_test' => $test->id,
                            'noi_dung' => $questionData['noi_dung'] ?? $questionData['content'] ?? '',
                            'mo_ta_chi_tiet' => $questionData['mo_ta_chi_tiet'] ?? '',
                            'loai_cau_hoi' => $questionData['loai_cau_hoi'] ?? $questionData['type'] ?? 'multiple_choice',
                            'hinh_anh_url' => $questionData['hinh_anh_url'] ?? null,
                            'ghi_chu' => $questionData['ghi_chu'] ?? null,
                            'thu_tu' => $index + 1,
                            'diem_max' => $questionData['diem_max'] ?? $questionData['diem_toi_da'] ?? 1,
                        ]);

                        // Handle audio upload if present
                        $audioKey = 'audio_' . $index;
                        if ($request->hasFile($audioKey)) {
                            $this->handleQuestionAudioUpload($request, $audioKey, $question);
                        }

                        // Create answers for new question
                        if (isset($questionData['answers']) && is_array($questionData['answers'])) {
                            foreach ($questionData['answers'] as $answerIndex => $answerData) {
                                DapAn::create([
                                    'id_cau_hoi' => $question->id,
                                    'noi_dung' => $answerData['noi_dung'] ?? $answerData['content'] ?? '',
                                    'hinh_anh_url' => $answerData['hinh_anh_url'] ?? null,
                                    'mo_ta_chi_tiet' => $answerData['mo_ta_chi_tiet'] ?? null,
                                    'la_dap_an_dung' => $answerData['la_dap_an_dung'] ?? $answerData['correct'] ?? false,
                                    'diem_tu_dong' => $answerData['diem_tu_dong'] ?? 0,
                                    'thu_tu' => $answerIndex + 1,
                                ]);
                            }
                        }
                        
                        // IMPORTANT: Add the newly created question to the list of existing IDs
                        // so it's not immediately deleted by the cleanup routine below!
                        $existingQuestionIds[] = $question->id;
                    }
                }

                // Delete questions not in the update (questions that were removed)
                if (count($existingQuestionIds) > 0) {
                    CauHoi::where('id_bai_test', $test->id)
                        ->whereNotIn('id', $existingQuestionIds)
                        ->get()
                        ->each(function ($q) {
                            $q->dapAns()->delete();
                            $q->delete();
                        });
                } else {
                    // If no existing questions, delete all (user removed all questions)
                    CauHoi::where('id_bai_test', $test->id)
                        ->get()
                        ->each(function ($q) {
                            $q->dapAns()->delete();
                            $q->delete();
                        });
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật bài test thành công',
                'data' => new BaiTestResource($test),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật bài test: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Xóa bài test (giáo viên)
     */
    public function destroy($id)
    {
        try {
            $test = BaiTest::find($id);

            if (!$test) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài test không tồn tại',
                ], 404);
            }

            $currentUserId = auth('sanctum')->id();
            $currentUser = auth('sanctum')->user();
            $isAdmin = $currentUser && ($currentUser->role === 'admin' || $currentUser->role === 2);

            if ((int)$test->id_giao_vien !== (int)$currentUserId && !$isAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa bài test này',
                ], 403);
            }

            $test->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa bài test thành công',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa bài test: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bắt đầu làm bài test (học sinh)
     */
    public function startTest($testId, Request $request)
    {
        $userId = auth('sanctum')->id();

        try {
            // Kiểm tra bài test tồn tại và đã published
            $test = BaiTest::published()->find($testId);
            if (!$test) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài test không tồn tại hoặc chưa được công bố',
                ], 404);
            }

            // Kiểm tra thời gian availability
            if (!$this->isTestAvailable($test)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài test này không còn khả dụng',
                ], 403);
            }

            // Kiểm tra học sinh đã enroll khóa học chứa bài test (bỏ qua cho giáo viên/admin)
            $user = auth('sanctum')->user();
            if ($user && $user->role === 'hoc_sinh') {
                $enrolled = CourseEnrollment::where('id_hoc_sinh', $userId)
                    ->where('id_lesson', $test->id_lesson)
                    ->exists();

                if (!$enrolled) {
                    // Auto-enroll student for convenience
                    CourseEnrollment::create([
                        'id_hoc_sinh' => $userId,
                        'id_lesson' => $test->id_lesson,
                        'ngay_dang_ky' => now(),
                    ]);
                }
            }

            // Kiểm tra số lần làm đã vượt quá giới hạn
            if ($test->so_lan_lam_toi_da > 0) {
                $attemptCount = StudentTestResult::where('id_hoc_sinh', $userId)
                    ->where('id_bai_test', $testId)
                    ->whereIn('trang_thai', ['completed', 'pending_review', 'grading', 'expired'])
                    ->count();

                if ($attemptCount >= $test->so_lan_lam_toi_da) {
                    return response()->json([
                        'success' => false,
                        'message' => "Bạn đã hết số lần làm bài test này (tối đa {$test->so_lan_lam_toi_da} lần)",
                    ], 403);
                }
            }

            // Tạo hoặc lấy attempt hiện tại (chưa submit)
            $result = StudentTestResult::where('id_hoc_sinh', $userId)
                ->where('id_bai_test', $testId)
                ->where('trang_thai', 'in_progress')
                ->first();

            // Nếu attempt cũ đã quá giờ thì đóng attempt đó và tạo attempt mới
            if ($result && $test->thoi_gian_toi_da > 0 && $result->thoi_gian_bat_dau) {
                $maxSeconds = $test->thoi_gian_toi_da * 60;
                $elapsedSeconds = max(0, $result->thoi_gian_bat_dau->diffInSeconds(now(), false));

                if ($elapsedSeconds >= $maxSeconds) {
                    $result->update([
                        'trang_thai' => 'expired',
                        'thoi_gian_hoan_thanh' => now(),
                        'thoi_gian_su_dung' => $maxSeconds,
                    ]);
                    $result = null;
                }
            }

            if (!$result) {
                // Tính lan_thu (attempt number)
                $lanThu = StudentTestResult::where('id_hoc_sinh', $userId)
                    ->where('id_bai_test', $testId)
                    ->max('lan_thu') ?? 0;
                $lanThu += 1;

                $result = StudentTestResult::create([
                    'id_hoc_sinh' => $userId,
                    'id_bai_test' => $testId,
                    'lan_thu' => $lanThu,
                    'trang_thai' => 'in_progress',
                    'thoi_gian_bat_dau' => now(),
                ]);
            }

            $elapsedSeconds = 0;
            if ($result->thoi_gian_bat_dau) {
                $elapsedSeconds = max(0, $result->thoi_gian_bat_dau->diffInSeconds(now(), false));
            }
            $remainingSeconds = $test->thoi_gian_toi_da > 0
                ? max(($test->thoi_gian_toi_da * 60) - $elapsedSeconds, 0)
                : null;

            return response()->json([
                'success' => true,
                'message' => 'Bắt đầu làm bài test',
                'data' => [
                    'id' => $result->id,
                    'id_bai_test' => $result->id_bai_test,
                    'lan_thu' => $result->lan_thu,
                    'trang_thai' => $result->trang_thai,
                    'thoi_gian_bat_dau' => $result->thoi_gian_bat_dau,
                    'thoi_gian_toi_da' => $test->thoi_gian_toi_da,
                    'thoi_gian_con_lai' => $remainingSeconds,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi bắt đầu bài test: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Nộp bài test (học sinh) - với auto-grading mở rộng
     */
    public function submitTest(SubmitTestRequest $request, $testId)
    {
        $userId = auth('sanctum')->id();

        try {
            // Kiểm tra bài test tồn tại
            $test = BaiTest::published()->find($testId);
            if (!$test) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài test không tồn tại',
                ], 404);
            }

            // Kiểm tra StudentTestResult có tồn tại và đang in_progress
            $result = StudentTestResult::where('id_hoc_sinh', $userId)
                ->where('id_bai_test', $testId)
                ->where('trang_thai', 'in_progress')
                ->first();

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn chưa bắt đầu bài test này hoặc đã nộp bài',
                ], 404);
            }

            // Kiểm tra thời gian giới hạn
            if ($test->thoi_gian_toi_da > 0) {
                $elapsedSeconds = $result->thoi_gian_bat_dau
                    ? max(0, $result->thoi_gian_bat_dau->diffInSeconds(now(), false))
                    : 0;
                $maxSeconds = $test->thoi_gian_toi_da * 60; // Convert to seconds

                if ($elapsedSeconds > $maxSeconds) {
                    $result->update([
                        'trang_thai' => 'expired',
                        'thoi_gian_hoan_thanh' => now(),
                        'thoi_gian_su_dung' => $maxSeconds,
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Thời gian làm bài đã hết',
                    ], 403);
                }
            }

            // Lấy danh sách câu hỏi hợp lệ của bài test
            $validQuestions = CauHoi::where('id_bai_test', $testId)
                ->with('dapAns')
                ->get()
                ->keyBy('id');

            // Use transaction to ensure data integrity
            DB::transaction(function () use ($request, $result, $test, $validQuestions) {
                $totalScore = 0;
                $correctAnswers = 0;
                $wrongAnswers = 0;
                $unanswered = 0;

                foreach ($validQuestions as $question) {
                    // Find student's answer for this question
                    $studentAnswer = collect($request->answers)->firstWhere('id_cau_hoi', $question->id);

                    if (!$studentAnswer) {
                        $unanswered++;
                        continue;
                    }

                    // Save/update student answer
                    StudentAnswerDetail::updateOrCreate(
                        [
                            'id_student_test_result' => $result->id,
                            'id_cau_hoi' => $question->id,
                        ],
                        [
                            'id_dap_an' => $studentAnswer['id_dap_an'] ?? null,
                            'cau_tra_loi_tu_do' => $studentAnswer['cau_tra_loi_tu_do'] ?? null,
                        ]
                    );

                    // Auto-grade based on question type
                    $score = $this->gradeAnswer($question, $studentAnswer, $test);

                    if ($score > 0) {
                        $correctAnswers++;
                        $totalScore += $score;
                    } else {
                        $wrongAnswers++;
                    }

                    // Update StudentAnswerDetail dengan điểm
                    StudentAnswerDetail::where('id_student_test_result', $result->id)
                        ->where('id_cau_hoi', $question->id)
                        ->update(['diem_cau_hoi' => $score]);
                }

                // Calculate time used
                $timeUsed = $result->thoi_gian_bat_dau
                    ? max(0, $result->thoi_gian_bat_dau->diffInSeconds(now(), false))
                    : 0;

                // Update StudentTestResult
                $result->update([
                    'diem_tong' => round((float) $totalScore, 2),
                    'so_cau_dung' => $correctAnswers,
                    'so_cau_sai' => $wrongAnswers,
                    'so_cau_bo_trong' => $unanswered,
                    'thoi_gian_su_dung' => $timeUsed,
                    'thoi_gian_hoan_thanh' => now(),
                    'trang_thai' => 'completed',
                ]);

                // Update analytics
                $this->updateTestAnalytics($test->id, $result, $validQuestions);
            });

            $result->refresh();

            $responseData = [
                'id' => $result->id,
                'diem_tong' => $result->diem_tong,
                'so_cau_dung' => $result->so_cau_dung,
                'so_cau_sai' => $result->so_cau_sai,
                'so_cau_bo_trong' => $result->so_cau_bo_trong,
                'thoi_gian_su_dung' => $result->thoi_gian_su_dung,
                'trang_thai' => $result->trang_thai,
            ];

            // Include result display if configured
            if ($test->hien_thi_ket_qua_ngay_lap) {
                $responseData['result_display'] = $this->getResultDisplay($result, $test);
            }

            return response()->json([
                'success' => true,
                'message' => 'Nộp bài test thành công',
                'data' => $responseData,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi nộp bài test: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Xem kết quả test (học sinh) - với filter hiển thị
     */
    public function getResult($testId)
    {
        try {
            $userId = auth('sanctum')->id();
            $result = StudentTestResult::where('id_hoc_sinh', $userId)
                ->where('id_bai_test', $testId)
                ->whereIn('trang_thai', ['completed', 'pending_review', 'grading'])
                ->orderByDesc('thoi_gian_hoan_thanh')
                ->orderByDesc('id')
                ->first();

            if (!$result || !in_array($result->trang_thai, ['completed', 'pending_review', 'grading'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kết quả test không tồn tại hoặc chưa được chấm',
                ], 404);
            }

            $test = BaiTest::find($testId);

            // Check if student can review (allow if result is pending_review, or if test allows review)
            // Also allow immediate viewing if test is configured to show results immediately
            if (!$test->cho_xem_lai_test && !$test->hien_thi_ket_qua_ngay_lap && $result->trang_thai !== 'pending_review') {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không được phép xem lại bài test này',
                ], 403);
            }

            $answers = $result->studentAnswerDetails()
                ->with('cauHoi', 'dapAn')
                ->get()
                ->map(function ($answer) use ($test) {
                    $data = [
                        'id_cau_hoi' => $answer->id_cau_hoi,
                        'noi_dung_cau_hoi' => $answer->cauHoi->noi_dung,
                        'audio_url' => $answer->cauHoi->audio_url ? url($answer->cauHoi->audio_url) : null,
                        'hinh_anh_url' => $answer->cauHoi->hinh_anh_url,
                        'loai_cau_hoi' => $answer->cauHoi->loai_cau_hoi,
                        'diem_max' => $answer->cauHoi->diem_max,
                        'diem_tong' => $answer->diem_cau_hoi ?? 0,
                        'dap_an_chon' => $answer->dapAn ? $answer->dapAn->noi_dung : $answer->cau_tra_loi_tu_do,
                    ];

                    // Show correct answer if configured
                    if ($test->hien_thi_dap_an_dung) {
                        $correctAnswer = $answer->cauHoi->dapAns->firstWhere('la_dap_an_dung', true);
                        $data['dap_an_dung'] = $correctAnswer ? $correctAnswer->noi_dung : null;
                    }

                    return $data;
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $result->id,
                    'diem_tong' => $result->diem_tong,
                    'so_cau_dung' => $result->so_cau_dung,
                    'so_cau_sai' => $result->so_cau_sai,
                    'so_cau_bo_trong' => $result->so_cau_bo_trong,
                    'thoi_gian_su_dung' => $result->thoi_gian_su_dung,
                    'lan_thu' => $result->lan_thu,
                    'trang_thai' => $result->trang_thai,
                    'ghi_chu_giao_vien' => $result->ghi_chu_giao_vien,
                    'thoi_gian_hoan_thanh' => $result->thoi_gian_hoan_thanh,
                    'chi_tiet_tung_cau' => $answers,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy kết quả test: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get test analytics/statistics (giáo viên)
     */
    public function getAnalytics($testId)
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
                    'message' => 'Bạn không có quyền xem thống kê bài test này',
                ], 403);
            }

            $analytics = TestAnalytic::firstOrCreate(['id_bai_test' => $testId]);

            // Get detailed question analytics
            $questionAnalytics = QuestionAnalytic::where('id_bai_test', $testId)
                ->with('question:id,noi_dung,diem_max,loai_cau_hoi')
                ->get();

            // Get student attempts
            $attempts = StudentTestResult::where('id_bai_test', $testId)
                ->where('trang_thai', 'completed')
                ->orderBy('diem_tong', 'desc')
                ->get(['id', 'id_hoc_sinh', 'diem_tong', 'so_cau_dung', 'thoi_gian_su_dung', 'lan_thu', 'created_at']);

            return response()->json([
                'success' => true,
                'data' => [
                    'test_id' => $test->id,
                    'ten_bai_test' => $test->ten_bai_test,
                    'analytics' => [
                        'so_hoc_sinh_lam' => $analytics->so_hoc_sinh_lam,
                        'diem_trung_binh' => $analytics->diem_trung_binh,
                        'diem_min' => $analytics->diem_min,
                        'diem_max' => $analytics->diem_max,
                        'ty_le_hoc_sinh_dau' => $analytics->ty_le_hoc_sinh_dau,
                        'thoi_gian_trung_binh' => $analytics->thoi_gian_trung_binh,
                    ],
                    'question_analytics' => $questionAnalytics->map(function ($qa) {
                        return [
                            'id_cau_hoi' => $qa->id_cau_hoi,
                            'noi_dung' => $qa->question->noi_dung,
                            'so_hoc_sinh_lam' => $qa->so_hoc_sinh_lam,
                            'ty_le_tra_loi_dung' => $qa->ty_le_tra_loi_dung,
                            'do_kho' => $qa->do_kho,
                            'diem_trung_binh' => $qa->diem_trung_binh,
                        ];
                    }),
                    'student_attempts' => $attempts->map(function ($attempt) {
                        return [
                            'id' => $attempt->id,
                            'id_hoc_sinh' => $attempt->id_hoc_sinh,
                            'diem_tong' => $attempt->diem_tong,
                            'so_cau_dung' => $attempt->so_cau_dung,
                            'thoi_gian_su_dung' => $attempt->thoi_gian_su_dung,
                            'lan_thu' => $attempt->lan_thu,
                            'created_at' => $attempt->created_at,
                        ];
                    }),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy thống kê bài test: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ==================== HELPER METHODS ====================
     */

    /**
     * Grade an answer based on question type
     */
    private function gradeAnswer($question, $studentAnswer, $test)
    {
        $score = 0;

        switch ($question->loai_cau_hoi) {
            case 'multiple_choice':
            case 'true_false':
            case 'image_choice':
                // Check if selected answer is correct
                $selectedAnswer = $question->dapAns->firstWhere('id', $studentAnswer['id_dap_an']);
                if ($selectedAnswer && $selectedAnswer->la_dap_an_dung) {
                    $score = $question->diem_max;
                }
                break;

            case 'matching':
            case 'fill_blank':
                // Partial grading possible
                $score = $this->gradePartialAnswer($question, $studentAnswer);
                break;

            case 'essay':
                // Manual grading needed
                $score = 0; // Will be set by teacher
                break;

            default:
                $score = 0;
        }

        return min($score, $question->diem_max);
    }

    /**
     * Grade partial answers (matching, fill-in-blank)
     */
    private function gradePartialAnswer($question, $studentAnswer)
    {
        $correctAnswers = $question->dapAns->filter(function ($a) {
            return $a->la_dap_an_dung;
        });

        $pointPerCorrect = $correctAnswers->count() > 0 ? $question->diem_max / $correctAnswers->count() : 0;
        $score = 0;

        foreach ($correctAnswers as $correct) {
            if ($studentAnswer['id_dap_an'] === $correct->id) {
                $score += $pointPerCorrect;
                break;
            }
        }

        return $score;
    }

    /**
     * Check if test is available (time-wise)
     */
    private function isTestAvailable($test)
    {
        $now = now();

        if ($test->ngay_bat_dau && $now < $test->ngay_bat_dau) {
            return false;
        }

        if ($test->ngay_ket_thuc && $now > $test->ngay_ket_thuc) {
            return false;
        }

        return true;
    }

    /**
     * Update test analytics after submission
     */
    private function updateTestAnalytics($testId, $result, $validQuestions)
    {
        $analytics = TestAnalytic::firstOrCreate(['id_bai_test' => $testId]);
        $test = BaiTest::find($testId);

        $completedResults = StudentTestResult::where('id_bai_test', $testId)
            ->where('trang_thai', 'completed')
            ->get();

        $totalScore = $completedResults->sum('diem_tong');
        $totalTime = $completedResults->sum('thoi_gian_su_dung');
        $passMark = $test ? ($test->diem_tong_max * 50) / 100 : 50; // 50% để pass
        $passCount = $completedResults->where('diem_tong', '>=', $passMark)->count();

        $analytics->update([
            'so_hoc_sinh_lam' => $completedResults->count(),
            'diem_trung_binh' => $completedResults->count() > 0 ? round($totalScore / $completedResults->count(), 2) : 0,
            'diem_min' => $completedResults->min('diem_tong') ?? 0,
            'diem_max' => $completedResults->max('diem_tong') ?? 0,
            'ty_le_hoc_sinh_dau' => $completedResults->count() > 0 ? round(($passCount / $completedResults->count()) * 100, 2) : 0,
            'thoi_gian_trung_binh' => $completedResults->count() > 0 ? round($totalTime / $completedResults->count(), 0) : 0,
        ]);

        // Update question analytics
        foreach ($validQuestions as $question) {
            $this->updateQuestionAnalytics($question, $testId);
        }
    }

    /**
     * Update question analytics
     */
    private function updateQuestionAnalytics($question, $testId)
    {
        $answers = StudentAnswerDetail::where('id_cau_hoi', $question->id)
            ->whereHas('studentTestResult', function ($q) use ($testId) {
                $q->where('id_bai_test', $testId)->where('trang_thai', 'completed');
            })
            ->get();

        $totalAnswers = $answers->count();
        $correctAnswers = $answers->where('diem_cau_hoi', '>', 0)->count();

        $analytics = QuestionAnalytic::firstOrCreate([
            'id_cau_hoi' => $question->id,
            'id_bai_test' => $testId,
        ]);

        $analytics->update([
            'so_hoc_sinh_lam' => $totalAnswers,
            'so_hoc_sinh_tra_loi_dung' => $correctAnswers,
            'ty_le_tra_loi_dung' => $totalAnswers > 0 ? round(($correctAnswers / $totalAnswers) * 100, 2) : 0,
            'do_kho' => $totalAnswers > 0 ? round((1 - ($correctAnswers / $totalAnswers)) * 100, 2) : 0,
            'diem_trung_binh' => $totalAnswers > 0 ? round($answers->sum('diem_cau_hoi') / $totalAnswers, 2) : 0,
        ]);
    }

    /**
     * Format result display
     */
    private function getResultDisplay($result, $test)
    {
        return [
            'diem_tong' => $result->diem_tong,
            'diem_tong_max' => $test->diem_tong_max,
            'ty_le_phan_tram' => round(($result->diem_tong / $test->diem_tong_max) * 100, 2),
            'so_cau_dung' => $result->so_cau_dung,
            'so_cau_sai' => $result->so_cau_sai,
            'so_cau_bo_trong' => $result->so_cau_bo_trong,
            'thoi_gian_su_dung_phut' => round($result->thoi_gian_su_dung / 60, 2),
        ];
    }

    /**
     * Handle question audio file upload
     */
    private function handleQuestionAudioUpload($request, $audioKey, $question)
    {
        try {
            if (!$request->hasFile($audioKey)) {
                return;
            }

            $file = $request->file($audioKey);

            // Validate file
            if ($file->getSize() > 50 * 1024 * 1024) { // 50MB max
                throw new \Exception('Dung lượng file không được vượt quá 50MB');
            }

            // Accept MP3, WAV, OGG
            $validMimeTypes = ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/x-wav'];
            $validExtensions = ['mp3', 'wav', 'ogg'];
            $extension = strtolower($file->getClientOriginalExtension());

            if (!in_array($file->getMimeType(), $validMimeTypes) && !in_array($extension, $validExtensions)) {
                throw new \Exception('Chỉ chấp nhận file audio: MP3, WAV, OGG');
            }

            // Remove old audio file if exists
            if ($question->audio_file_name && Storage::disk('public')->exists('audio/questions/' . $question->audio_file_name)) {
                Storage::disk('public')->delete('audio/questions/' . $question->audio_file_name);
            }

            // Store new audio file with extension
            $filename = 'question_' . $question->id . '_' . time() . '.' . $extension;
            $path = $file->storeAs('audio/questions', $filename, 'public');

            if (!$path) {
                throw new \Exception('Không thể lưu file audio');
            }

            // Build absolute URL from current API origin to avoid APP_URL mismatch issues.
            $audioUrl = rtrim($request->getSchemeAndHttpHost(), '/') . '/storage/' . ltrim($path, '/');

            // Log for debugging
            Log::debug('Audio path information', [
                'storage_path' => $path,
                'filename' => $filename,
                'base_path' => '/storage/audio/questions/',
                'full_url' => $audioUrl,
            ]);

            // Update question with audio info
            $question->update([
                'audio_file_name' => $filename,
                'audio_file_size' => $file->getSize(),
                'audio_url' => $audioUrl,
            ]);

            Log::info('Audio file saved successfully', [
                'question_id' => $question->id,
                'filename' => $filename,
                'path' => $path,
                'audio_url' => $audioUrl,
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the entire request
            Log::error('Error uploading question audio: ' . $e->getMessage(), ['audio_key' => $audioKey, 'question_id' => $question->id]);
        }
    }

    /**
     * Remove question audio files
     */
    private function removeQuestionAudio($question)
    {
        try {
            if ($question->audio_file_name) {
                // Delete file from storage
                if (Storage::disk('public')->exists('audio/questions/' . $question->audio_file_name)) {
                    Storage::disk('public')->delete('audio/questions/' . $question->audio_file_name);
                }

                // Update question - remove audio info
                $question->update([
                    'audio_file_name' => null,
                    'audio_file_size' => 0,
                    'audio_url' => null,
                ]);
            }
        } catch (\Exception $e) {
            // Log error but don't fail
            Log::error('Error removing question audio: ' . $e->getMessage());
        }
    }

    /**
     * Normalize question audio URL for client playback.
     */
    private function normalizeQuestionAudioUrl($audioUrl, Request $request)
    {
        if (!$audioUrl || !is_string($audioUrl)) {
            return null;
        }

        $audioUrl = trim($audioUrl);
        if ($audioUrl === '' || str_starts_with($audioUrl, 'blob:')) {
            return null;
        }

        $origin = rtrim($request->getSchemeAndHttpHost(), '/');

        if (str_starts_with($audioUrl, '/storage/')) {
            return $origin . $audioUrl;
        }

        if (str_starts_with($audioUrl, 'storage/')) {
            return $origin . '/' . $audioUrl;
        }

        if (preg_match('/^https?:\/\//i', $audioUrl)) {
            $path = parse_url($audioUrl, PHP_URL_PATH);
            if ($path && str_starts_with($path, '/storage/')) {
                return $origin . $path;
            }
        }

        return $audioUrl;
    }

    /**
     * Parse questions data from request (handles both JSON string and array format)
     */
    private function parseQuestionsData($request)
    {
        if (!$request->has('questions')) {
            return null;
        }

        $questionsData = $request->questions;

        // If questions is a JSON string, decode it
        if (is_string($questionsData)) {
            $decoded = json_decode($questionsData, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $questionsData = $decoded;
            }
        }

        return is_array($questionsData) ? $questionsData : null;
    }
}
