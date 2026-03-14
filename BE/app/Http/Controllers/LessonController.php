<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLessonRequest;
use App\Models\Lesson;
use App\Http\Resources\LessonResource;
use Illuminate\Http\Request;

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
            $lesson = Lesson::published()
                ->with('giaoVien:id,name,email')
                ->find($id);

            if (!$lesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài học không tồn tại',
                ], 404);
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
            $lesson = Lesson::create([
                'id_giao_vien' => auth('sanctum')->id(),
                'tieu_de' => $request->tieu_de,
                'mo_ta' => $request->mo_ta,
                'noi_dung' => $request->noi_dung,
                'trang_thai' => $request->trang_thai,
            ]);

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

            if ($lesson->id_giao_vien !== auth('sanctum')->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền cập nhật bài học này',
                ], 403);
            }

            $lesson->update([
                'tieu_de' => $request->tieu_de,
                'mo_ta' => $request->mo_ta,
                'noi_dung' => $request->noi_dung,
                'trang_thai' => $request->trang_thai,
            ]);

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

            if ($lesson->id_giao_vien !== auth('sanctum')->id()) {
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
            $lessons = Lesson::where('id_giao_vien', auth('sanctum')->id())->get();

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
}
