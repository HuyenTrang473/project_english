# Hướng dẫn sử dụng Admin Test Management

## Tổng quan

Chức năng **Quản lý Đề thi cho Admin** cho phép quản trị viên hệ thống có thể:

- Xem danh sách tất cả đề thi
- Tạo mới đề thi
- Cập nhật thông tin đề thi
- Xóa đề thi
- Lọc và tìm kiếm đề thi

## Cấu trúc thực hiện

### Backend (Laravel)

#### AdminController Methods

1. **getTeachers()** - GET /admin/teachers
    - Lấy danh sách tất cả giáo viên
    - Trả về: id, name, email

2. **getAllTests()** - GET /admin/tests
    - Lấy danh sách tất cả đề thi
    - Hỗ trợ filter:
        - search: tìm kiếm theo tên đề thi
        - status: lọc theo trạng thái (1: Bản nháp, 2: Đã xuất bản)
        - teacher_id: lọc theo giáo viên
        - lesson_id: lọc theo bài học
    - Hỗ trợ sort: sort_by, sort_order
    - Hỗ trợ pagination: per_page

3. **getTestDetail($id)** - GET /admin/tests/{id}
    - Lấy chi tiết một đề thi
    - Bao gồm thông tin giáo viên, bài học, danh sách câu hỏi

4. **createTest()** - POST /admin/tests
    - Tạo mới một đề thi
    - Yêu cầu các trường:
        - id_giao_vien (required)
        - id_lesson (required)
        - ten_bai_test (required)
        - mo_ta (optional)
        - thoi_gian_toi_da (required, 1-1440 phút)
        - diem_tong_max (required, 0.01-10000)
        - trang_thai (required, 1 hoặc 2)
        - so_lan_lam_toi_da (optional)
        - co_xao_tron_cau_hoi (optional)
        - co_xao_tron_dap_an (optional)
        - hien_thi_ket_qua_ngay_lap (optional)
        - hien_thi_dap_an_dung (optional)
        - cho_xem_lai_test (optional)
        - ngay_bat_dau (optional, format: Y-m-d H:i:s)
        - ngay_ket_thuc (optional, format: Y-m-d H:i:s)

5. **updateTest()** - PUT /admin/tests/{id}
    - Cập nhật thông tin đề thi
    - Tất cả trường là optional

6. **deleteTest($id)** - DELETE /admin/tests/{id}
    - Xóa một đề thi

### API Routes

Tất cả routes được bảo vệ bởi middleware `auth:sanctum` và `role:admin`:

```
GET    /admin/teachers           - Lấy danh sách giáo viên
GET    /admin/tests              - Lấy danh sách đề thi
GET    /admin/tests/{id}         - Lấy chi tiết đề thi
POST   /admin/tests              - Tạo mới đề thi
PUT    /admin/tests/{id}         - Cập nhật đề thi
DELETE /admin/tests/{id}         - Xóa đề thi
```

### Frontend (Vue.js)

#### AdminTestManager Component

File: `FE/src/components/Admin/AdminTestManager.vue`

**Tính năng:**

- Hiển thị danh sách đề thi trong bảng
- Tìm kiếm theo tên đề thi
- Lọc theo trạng thái
- Pagination
- Tạo mới đề thi (modal form)
- Chỉnh sửa đề thi (modal form)
- Xóa đề thi (xác nhận)

**Props:** Không có

**Events:** Không có

**Data:**

- tests: Danh sách đề thi
- teachers: Danh sách giáo viên
- lessons: Danh sách bài học
- formData: Dữ liệu form tạo/cập nhật
- pagination: Thông tin phân trang
- errors: Lỗi validation

**Methods:**

- loadTests() - Tải danh sách đề thi
- loadTeachers() - Tải danh sách giáo viên
- loadLessons() - Tải danh sách bài học
- openCreateDialog() - Mở dialog tạo mới
- openEditDialog(test) - Mở dialog chỉnh sửa
- saveTest() - Lưu đề thi (tạo hoặc cập nhật)
- deleteTest(testId) - Xóa đề thi
- handleSearch() - Xử lý tìm kiếm
- handleFilterChange() - Xử lý lọc

## Hướng dẫn sử dụng

### Đối với Admin

#### 1. Truy cập Admin Panel

- Đăng nhập với tài khoản admin
- Theo dõi menu sidebar, chọn "Quản lý Đề thi" (icon 📝)

#### 2. Xem danh sách đề thi

- Danh sách tất cả đề thi sẽ được hiển thị
- Tìm kiếm: Nhập tên đề thi vào ô tìm kiếm
- Lọc: Chọn trạng thái từ dropdown (Bản nháp, Đã xuất bản)
- Tải lại: Nhấn nút "Tải lại" để cập nhật dữ liệu

#### 3. Tạo mới đề thi

- Nhấn nút "+ Tạo mới đề thi"
- Điền thông tin:
    - **Giáo viên** (bắt buộc): Chọn giáo viên tạo đề thi
    - **Bài học** (bắt buộc): Chọn bài học liên quan
    - **Tên đề thi** (bắt buộc): Nhập tên
    - **Mô tả**: Nhập mô tả về đề thi
    - **Thời gian tối đa** (bắt buộc): Nhập số phút (1-1440)
    - **Điểm tối đa** (bắt buộc): Nhập điểm tối đa
    - **Số lần làm tối đa**: Nhập số lần học sinh có thể làm
    - **Trạng thái**: Chọn Bản nháp hoặc Đã xuất bản
    - **Cài đặt khác**:
        - Xáo trộn câu hỏi
        - Xáo trộn đáp án
        - Hiển thị kết quả ngay lập tức
        - Hiển thị đáp án đúng
        - Cho xem lại bài thi
    - **Ngày bắt đầu/kết thúc**: Đặt thời gian khả dụng
- Nhấn "Lưu" để tạo đề thi

#### 4. Cập nhật đề thi

- Nhấn icon ✏️ trên dòng đề thi
- Cập nhật thông tin cần thay đổi
- Nhấn "Lưu" để lưu thay đổi

#### 5. Xóa đề thi

- Nhấn icon 🗑️ trên dòng đề thi
- Xác nhận xóa trong popup

### Lưu ý quan trọng

1. **Trạng thái đề thi**:
    - 1 = Bản nháp: Không hiển thị cho học sinh
    - 2 = Đã xuất bản: Hiển thị cho học sinh

2. **Thời gian**:
    - Thời gian tối đa: 1-1440 phút (24 giờ)
    - Định dạng ngày/giờ: YYYY-MM-DDTHH:mm

3. **Điểm số**:
    - Điểm tối đa: 0.01-10000

4. **Validation**:
    - Tất cả trường bắt buộc phải được điền
    - Nếu có lỗi, thông báo sẽ hiển thị dưới trường input

## Troubleshooting

### Lỗi "Danh sách giáo viên trống"

- Kiểm tra xem đã tạo tài khoản giáo viên chưa
- Đảm bảo giáo viên có role là "giao_vien"

### Lỗi "Danh sách bài học trống"

- Kiểm tra xem đã tạo bài học chưa
- Truy cập API `/lessons` để tải danh sách

### Lỗi "Validation failed"

- Kiểm tra lại các trường bắt buộc
- Đảm bảo định dạng dữ liệu đúng
- Thông báo lỗi sẽ hiển thị chi tiết

### Pagination không hoạt động

- Tải lại trang
- Kiểm tra kết nối mạng

## API Examples

### Tạo đề thi

```bash
curl -X POST http://localhost:8000/api/admin/tests \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "id_giao_vien": 2,
    "id_lesson": 1,
    "ten_bai_test": "Kiểm tra Tiếng Anh Unit 1",
    "mo_ta": "Bài kiểm tra về grammar và vocabulary",
    "thoi_gian_toi_da": 30,
    "diem_tong_max": 100,
    "trang_thai": 2,
    "so_lan_lam_toi_da": 3,
    "co_xao_tron_cau_hoi": true,
    "hien_thi_ket_qua_ngay_lap": true
  }'
```

### Cập nhật đề thi

```bash
curl -X PUT http://localhost:8000/api/admin/tests/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "ten_bai_test": "Kiểm tra Tiếng Anh Unit 1 (Updated)",
    "thoi_gian_toi_da": 45
  }'
```

### Xóa đề thi

```bash
curl -X DELETE http://localhost:8000/api/admin/tests/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Lấy danh sách (có filter)

```bash
curl http://localhost:8000/api/admin/tests?search=Unit 1&status=2&per_page=10&page=1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Mở rộng trong tương lai

- [ ] Quản lý câu hỏi từ admin
- [ ] Quản lý đáp án từ admin
- [ ] Xuất dữ liệu đề thi (CSV, PDF)
- [ ] Nhập dữ liệu đề thi (CSV, Excel)
- [ ] Thống kê chi tiết về kết quả thi
- [ ] Sao chép đề thi
