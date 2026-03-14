<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherRequest;
use App\Models\User;
use App\Models\Lesson;
use App\Models\BaiTest;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Tạo tài khoản giáo viên (chỉ Admin)
     */
    public function createTeacher(StoreTeacherRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'role' => 'giao_vien',
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo giáo viên thành công',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo giáo viên: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy thống kê hệ thống (chỉ Admin)
     */
    public function getStats()
    {
        try {
            $stats = [
                'lessons' => Lesson::count(),
                'tests' => BaiTest::count(),
                'teachers' => User::where('role', 'giao_vien')->count(),
                'students' => User::where('role', 'hoc_sinh')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy thống kê: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy danh sách giáo viên (Admin)
     */
    public function getTeachers()
    {
        try {
            $teachers = User::where('role', 'giao_vien')
                ->select('id', 'name', 'email')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $teachers,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách giáo viên: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy danh sách tất cả đề thi (Admin)
     */
    public function getAllTests(\Illuminate\Http\Request $request)
    {
        try {
            $query = BaiTest::with(['giaoVien:id,name', 'lesson:id,tieu_de']);

            // Filter by name/search
            if ($request->has('search') && $request->search) {
                $query->where('ten_bai_test', 'like', '%' . $request->search . '%');
            }

            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('trang_thai', $request->status);
            }

            // Filter by teacher
            if ($request->has('teacher_id') && $request->teacher_id) {
                $query->where('id_giao_vien', $request->teacher_id);
            }

            // Filter by lesson
            if ($request->has('lesson_id') && $request->lesson_id) {
                $query->where('id_lesson', $request->lesson_id);
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
                'data' => $tests->items(),
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
                'message' => 'Lỗi khi lấy danh sách đề thi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy chi tiết đề thi (Admin)
     */
    public function getTestDetail($id)
    {
        try {
            $test = BaiTest::with(['giaoVien:id,name', 'lesson:id,tieu_de', 'cauHois'])
                ->find($id);

            if (!$test) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đề thi không tồn tại',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $test,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy chi tiết đề thi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tạo mới đề thi (Admin)
     */
    public function createTest(\Illuminate\Http\Request $request)
    {
        try {
            $validated = $request->validate([
                'id_giao_vien' => ['required', 'integer', 'exists:users,id'],
                'id_lesson' => ['required', 'integer', 'exists:lessons,id'],
                'ten_bai_test' => ['required', 'string', 'max:255'],
                'mo_ta' => ['nullable', 'string', 'max:1000'],
                'thoi_gian_toi_da' => ['required', 'integer', 'min:1', 'max:1440'],
                'diem_tong_max' => ['required', 'numeric', 'min:0.01', 'max:10000'],
                'trang_thai' => ['required', 'integer', 'in:1,2'],
                'so_lan_lam_toi_da' => ['nullable', 'integer', 'min:1', 'max:100'],
                'co_xao_tron_cau_hoi' => ['nullable', 'boolean'],
                'co_xao_tron_dap_an' => ['nullable', 'boolean'],
                'hien_thi_ket_qua_ngay_lap' => ['nullable', 'boolean'],
                'hien_thi_dap_an_dung' => ['nullable', 'boolean'],
                'cho_xem_lai_test' => ['nullable', 'boolean'],
                'ngay_bat_dau' => ['nullable', 'date_format:Y-m-d H:i:s'],
                'ngay_ket_thuc' => ['nullable', 'date_format:Y-m-d H:i:s'],
            ]);

            $test = BaiTest::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Tạo đề thi thành công',
                'data' => $test->load(['giaoVien:id,name', 'lesson:id,tieu_de']),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo đề thi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cập nhật đề thi (Admin)
     */
    public function updateTest(\Illuminate\Http\Request $request, $id)
    {
        try {
            $test = BaiTest::find($id);

            if (!$test) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đề thi không tồn tại',
                ], 404);
            }

            $validated = $request->validate([
                'id_giao_vien' => ['nullable', 'integer', 'exists:users,id'],
                'id_lesson' => ['nullable', 'integer', 'exists:lessons,id'],
                'ten_bai_test' => ['nullable', 'string', 'max:255'],
                'mo_ta' => ['nullable', 'string', 'max:1000'],
                'thoi_gian_toi_da' => ['nullable', 'integer', 'min:1', 'max:1440'],
                'diem_tong_max' => ['nullable', 'numeric', 'min:0.01', 'max:10000'],
                'trang_thai' => ['nullable', 'integer', 'in:1,2'],
                'so_lan_lam_toi_da' => ['nullable', 'integer', 'min:1', 'max:100'],
                'co_xao_tron_cau_hoi' => ['nullable', 'boolean'],
                'co_xao_tron_dap_an' => ['nullable', 'boolean'],
                'hien_thi_ket_qua_ngay_lap' => ['nullable', 'boolean'],
                'hien_thi_dap_an_dung' => ['nullable', 'boolean'],
                'cho_xem_lai_test' => ['nullable', 'boolean'],
                'ngay_bat_dau' => ['nullable', 'date_format:Y-m-d H:i:s'],
                'ngay_ket_thuc' => ['nullable', 'date_format:Y-m-d H:i:s'],
            ]);

            $test->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật đề thi thành công',
                'data' => $test->load(['giaoVien:id,name', 'lesson:id,tieu_de']),
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật đề thi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Xóa đề thi (Admin)
     */
    public function deleteTest($id)
    {
        try {
            $test = BaiTest::find($id);

            if (!$test) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đề thi không tồn tại',
                ], 404);
            }

            $test->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa đề thi thành công',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa đề thi: ' . $e->getMessage(),
            ], 500);
        }
    }
}
