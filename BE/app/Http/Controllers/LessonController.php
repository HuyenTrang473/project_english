<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLessonRequest;
use App\Models\Lesson;
use App\Http\Resources\LessonResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    /**
     * Danh sách tất cả bài học (public)
     */
    public function index(Request $request)
    {
        try {
            $lessons = Lesson::published()
                ->with('giaoVien:id,name,email')
                ->get();

            return response()->json([
                'success' => true,
                'data' => LessonResource::collection($lessons),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách bài học: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy chi tiết bài học
     */
    public function show($id)
    {
        try {
            $lesson = Lesson::with('giaoVien:id,name,email')->find($id);

            if (!$lesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài học không tồn tại',
                ], 404);
            }

            // Check access control:
            // - If published: everyone can view
            // - If draft: only owner (giáo viên) or admin can view
            $user = auth('sanctum')->user();
            $isOwner = $lesson->id_giao_vien === auth('sanctum')->id();
            $isAdmin = $user && $user->role === 'admin';
            $isPublished = $lesson->trang_thai === 2;

            if (!$isPublished && !$isOwner && !$isAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xem bài học này',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => new LessonResource($lesson),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy chi tiết bài học: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tạo bài học mới (giáo viên)
     */
    public function store(StoreLessonRequest $request)
    {
        try {
            // Log incoming request
            Log::info('LessonController::store - Request data:', [
                'tieu_de' => $request->tieu_de,
                'mo_ta' => $request->mo_ta,
                'noi_dung' => $request->noi_dung,
                'noi_dung_length' => strlen($request->noi_dung ?? ''),
                'trang_thai' => $request->trang_thai,
                'has_file' => $request->hasFile('file'),
            ]);

            $data = [
                'id_giao_vien' => auth('sanctum')->id(),
                'tieu_de' => $request->tieu_de,
                'mo_ta' => $request->mo_ta,
                'noi_dung' => $request->noi_dung,
                'trang_thai' => $request->trang_thai,
            ];

            // Handle file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filepath = $file->store('lessons', 'public');
                $data['file_path'] = $filepath;
                $data['file_type'] = $file->extension();
                $data['file_size'] = $file->getSize();
            }

            $lesson = Lesson::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Tạo bài học thành công',
                'data' => new LessonResource($lesson),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo bài học: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cập nhật bài học (giáo viên - chỉ của mình)
     */
    public function update(StoreLessonRequest $request, $id)
    {
        try {
            $lesson = Lesson::find($id);

            if (!$lesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài học không tồn tại',
                ], 404);
            }

            $user = auth('sanctum')->user();
            if ($lesson->id_giao_vien !== auth('sanctum')->id() && !($user && $user->role === 'admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền cập nhật bài học này',
                ], 403);
            }

            $data = [
                'tieu_de' => $request->tieu_de,
                'mo_ta' => $request->mo_ta,
                'noi_dung' => $request->noi_dung,
                'trang_thai' => $request->trang_thai,
            ];

            // Handle file upload
            if ($request->hasFile('file')) {
                // Delete old file if exists
                if ($lesson->file_path && Storage::disk('public')->exists($lesson->file_path)) {
                    Storage::disk('public')->delete($lesson->file_path);
                }

                $file = $request->file('file');
                $filepath = $file->store('lessons', 'public');
                $data['file_path'] = $filepath;
                $data['file_type'] = $file->extension();
                $data['file_size'] = $file->getSize();
            }

            $lesson->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật bài học thành công',
                'data' => new LessonResource($lesson),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật bài học: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Xóa bài học (giáo viên)
     */
    public function destroy($id)
    {
        try {
            $lesson = Lesson::find($id);

            if (!$lesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài học không tồn tại',
                ], 404);
            }

            $user = auth('sanctum')->user();
            if ($lesson->id_giao_vien !== auth('sanctum')->id() && !($user && $user->role === 'admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa bài học này',
                ], 403);
            }

            $lesson->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa bài học thành công',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa bài học: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Danh sách bài học của giáo viên (giáo viên)
     */
    public function myLessons(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $query = Lesson::query();

            if ($user && $user->role !== 'admin') {
                $query->where('id_giao_vien', $user->id);
            }

            // Filter by status if provided
            if ($request->has('status')) {
                $query->where('trang_thai', $request->status);
            }

            // Filter by search term - title only
            if ($request->has('search')) {
                $query->where('tieu_de', 'like', "%{$request->search}%");
            }

            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $lessons = $query->get();

            return response()->json([
                'success' => true,
                'data' => LessonResource::collection($lessons),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách bài học: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy chi tiết bài học cho giáo viên (để edit, bao gồm cả draft)
     */
    public function showForTeacher($id)
    {
        try {
            $lesson = Lesson::find($id);

            if (!$lesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài học không tồn tại',
                ], 404);
            }

            // Check authorization
            $user = auth('sanctum')->user();
            if ($lesson->id_giao_vien !== auth('sanctum')->id() && !($user && $user->role === 'admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xem bài học này',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => new LessonResource($lesson->load('giaoVien')),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy chi tiết bài học: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Danh sách bài học theo giáo viên (public)
     */
    public function getByTeacher($teacherId, Request $request)
    {
        try {
            $query = Lesson::where('id_giao_vien', $teacherId)->published();

            // Search filter - title only
            if ($request->has('search')) {
                $query->where('tieu_de', 'like', "%{$request->search}%");
            }

            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $lessons = $query->with('giaoVien:id,name,email')->get();

            return response()->json([
                'success' => true,
                'data' => LessonResource::collection($lessons),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách bài học: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lọc bài học (public)
     */
    public function filter(Request $request)
    {
        try {
            $query = Lesson::published();

            // Search by title only
            if ($request->has('search')) {
                $query->where('tieu_de', 'like', "%{$request->search}%");
            }

            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $page = $request->get('page', 1);
            $pageSize = $request->get('page_size', 10);
            $lessons = $query->with('giaoVien:id,name,email')
                ->paginate($pageSize, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => LessonResource::collection($lessons->items()),
                'pagination' => [
                    'total' => $lessons->total(),
                    'per_page' => $lessons->perPage(),
                    'current_page' => $lessons->currentPage(),
                    'last_page' => $lessons->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lọc bài học: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle publish/draft status của bài học
     */
    public function toggleStatus($id)
    {
        try {
            $lesson = Lesson::find($id);

            if (!$lesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài học không tồn tại',
                ], 404);
            }

            // Check authorization
            $user = auth('sanctum')->user();
            if ($lesson->id_giao_vien !== auth('sanctum')->id() && !($user && $user->role === 'admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền thay đổi trạng thái bài học này',
                ], 403);
            }

            // Toggle status: 1 (draft) <-> 2 (published)
            $lesson->trang_thai = $lesson->trang_thai === 1 ? 2 : 1;
            $lesson->save();

            return response()->json([
                'success' => true,
                'message' => $lesson->trang_thai === 2 ? 'Bài học đã được xuất bản' : 'Bài học đã chuyển sang nháp',
                'data' => new LessonResource($lesson),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi thay đổi trạng thái bài học: ' . $e->getMessage(),
            ], 500);
        }
    }
}
