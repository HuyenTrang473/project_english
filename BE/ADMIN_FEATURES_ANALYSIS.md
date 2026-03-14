# Phân Tích Chức Năng Quản Lý Admin, Bài Học, Đề Thi

## 📋 TÓM TẮT TỔNG QUÁT

Hệ thống hiện tại sử dụng **Laravel + Vue.js** với **3 vai trò chính**:

- **Admin**: Quản lý hệ thống
- **Giáo viên (Giao Vien)**: Tạo bài học, đề thi, câu hỏi
- **Học sinh (Hoc Sinh)**: Tham gia học tập, làm bài test

---

## 1️⃣ CHỨC NĂNG ADMIN HIỆN TẠI

### 1.1 **Các API Endpoints Admin**

```
POST   /admin/teachers         - Tạo tài khoản giáo viên
GET    /admin/stats            - Lấy thống kê hệ thống
```

### 1.2 **Chi Tiết Các Chức Năng**

#### 1.2.1 Tạo Tài Khoản Giáo Viên

- **Endpoint**: `POST /admin/teachers`
- **Middleware**: `auth:sanctum, role:admin`
- **Chức năng**:
    - Tạo user mới với role `giao_vien`
    - Đặt `is_active = true`
    - Trả về thông tin giáo viên (id, name, email, role)
- **Dữ liệu nhập**: `name`, `email`, `password`
- **Validation**: Sử dụng `StoreTeacherRequest`

#### 1.2.2 Lấy Thống Kê Hệ Thống

- **Endpoint**: `GET /admin/stats`
- **Middleware**: `auth:sanctum, role:admin`
- **Thống kê trả về**:
    - `lessons`: Tổng số bài học
    - `tests`: Tổng số bài test
    - `teachers`: Tổng số giáo viên (role: giao_vien)
    - `students`: Tổng số học sinh (role: hoc_sinh)

### 1.3 **⚠️ Chức Năng Admin Còn Thiếu**

| Chức Năng                          | Trạng Thái | Ghi Chú                    |
| ---------------------------------- | ---------- | -------------------------- |
| ✗ Liệt kê tất cả giáo viên         | THIẾU      | Cần phân trang             |
| ✗ Liệt kê tất cả học sinh          | THIẾU      | Cần phân trang             |
| ✗ Xem chi tiết user                | THIẾU      | -                          |
| ✗ Vô hiệu hóa/kích hoạt user       | THIẾU      | Thay đổi `is_active`       |
| ✗ Xóa user                         | THIẾU      | Soft delete or hard delete |
| ✗ Phân quyền/cấp vai trò           | THIẾU      | -                          |
| ✗ Thống kê chi tiết (activity log) | THIẾU      | Phân tích hành động user   |
| ✗ Quản lý ghi nhật ký hệ thống     | THIẾU      | -                          |
| ✗ Backup/Export dữ liệu            | THIẾU      | -                          |

---

## 2️⃣ CHỨC NĂNG QUẢN LÝ BÀI HỌC

### 2.1 **Các API Endpoints Bài Học**

```
GET    /lessons                        - Danh sách bài học công khai
GET    /lessons/{id}                   - Chi tiết bài học
POST   /lessons                        - Tạo bài học mới (giáo viên)
PUT    /lessons/{id}                   - Cập nhật bài học (giáo viên)
DELETE /lessons/{id}                   - Xóa bài học (giáo viên)
GET    /teacher/lessons                - Bài học của tôi (giáo viên)
```

### 2.2 **Chi Tiết Các Chức Năng**

#### 2.2.1 Liệt Kê Bài Học

- **Endpoint**: `GET /lessons` (Public)
- **Middleware**: Không cần authentication
- **Chức năng**:
    - Lấy danh sách bài học đã published (`trang_thai = 2`)
    - Kèm thông tin giáo viên (id, name, email)
    - Trả về dạng resource (LessonResource)
- **Lưu ý**: Chỉ hiển thị bài học công khai

#### 2.2.2 Chi Tiết Bài Học

- **Endpoint**: `GET /lessons/{id}` (Public)
- **Chức năng**:
    - Kiểm tra bài học phải published
    - Trả về đầy đủ thông tin bài học + giáo viên
- **Status code**: 404 nếu không tìm thấy

#### 2.2.3 Tạo Bài Học Mới

- **Endpoint**: `POST /lessons`
- **Middleware**: `auth:sanctum, role:giao_vien`
- **Dữ liệu nhập**:
    - `tieu_de`: Tiêu đề bài học (required)
    - `mo_ta`: Mô tả (required)
    - `noi_dung`: Nội dung bài học (required)
    - `trang_thai`: Trạng thái publish (1=draft, 2=published)
- **Giao viên tự động**: `id_giao_vien = auth()->id()`

#### 2.2.4 Cập Nhật Bài Học

- **Endpoint**: `PUT /lessons/{id}`
- **Middleware**: `auth:sanctum, role:giao_vien`
- **Kiểm tra quyền**: Phải là giáo viên sở hữu bài học này
- **Lỗi 403**: Nếu không phải chủ sở hữu

#### 2.2.5 Xóa Bài Học

- **Endpoint**: `DELETE /lessons/{id}`
- **Middleware**: `auth:sanctum, role:giao_vien`
- **Kiểm tra quyền**: Phải là giáo viên sở hữu bài học này

#### 2.2.6 Bài Học Của Tôi

- **Endpoint**: `GET /teacher/lessons`
- **Middleware**: `auth:sanctum, role:giao_vien`
- **Chức năng**: Lấy tất cả bài học của giáo viên hiện tại

### 2.3 **⚠️ Chức Năng Bài Học Còn Thiếu**

| Chức Năng                              | Trạng Thái | Ghi Chú                           |
| -------------------------------------- | ---------- | --------------------------------- |
| ✓ CRUD cơ bản                          | ✅         | Đã có                             |
| ✗ Bộ lọc/Tìm kiếm                      | THIẾU      | Theo tiêu đề, giáo viên, ngôn ngữ |
| ✗ Phân trang (Pagination)              | THIẾU      | Cần xử lý khi có nhiều bài        |
| ✗ Sắp xếp (Sorting)                    | THIẾU      | Mới nhất, cũ nhất, A-Z            |
| ✗ Lưu nháp (Draft)                     | THIẾU      | Tạo nháp nhưng chưa publish       |
| ✗ Hệ thống phiên bản (Version control) | THIẾU      | Lưu lịch sử chỉnh sửa             |
| ✗ Bình luận/Đánh giá bài học           | THIẾU      | Feedback từ học sinh              |
| ✗ Danh mục/Kategorize bài học          | THIẾU      | Phân loại theo chủ đề             |
| ✗ Thống kê: Lượt xem, engagement       | THIẾU      | Analytics per lesson              |

---

## 3️⃣ CHỨC NĂNG QUẢN LÝ ĐỀ THI

### 3.1 **Các API Endpoints Đề Thi**

```
GET    /lessons/{lessonId}/bai-tests              - Danh sách test của bài học
GET    /bai-tests/{id}                            - Chi tiết test
POST   /bai-tests                                 - Tạo test mới (giáo viên)
PUT    /bai-tests/{id}                            - Cập nhật test (giáo viên)
DELETE /bai-tests/{id}                            - Xóa test (giáo viên)
GET    /teacher/bai-tests                         - Các test của tôi
POST   /bai-tests/{testId}/start                  - Bắt đầu làm test (học sinh)
POST   /bai-tests/{testId}/submit                 - Nộp bài (học sinh)
GET    /bai-tests/{testId}/result                 - Xem kết quả (học sinh)

GET    /bai-tests/{testId}/cau-hois               - Danh sách câu hỏi
POST   /bai-tests/{testId}/cau-hois               - Tạo câu hỏi
PUT    /bai-tests/{testId}/cau-hois/{id}          - Cập nhật câu hỏi
DELETE /bai-tests/{testId}/cau-hois/{id}          - Xóa câu hỏi

POST   /bai-tests/{testId}/cau-hois/{id}/dap-ans  - Tạo đáp án
PUT    /bai-tests/{testId}/cau-hois/{id}/dap-ans/{id} - Cập nhật đáp án
DELETE /bai-tests/{testId}/cau-hois/{id}/dap-ans/{id} - Xóa đáp án
```

### 3.2 **Chi Tiết Các Chức Năng**

#### 3.2.1 Liệt Kê Test Của Bài Học

- **Endpoint**: `GET /lessons/{lessonId}/bai-tests`
- **Middleware**: Public
- **Scope**: Chỉ show test đã published (`trang_thai = 2`)
- **Return**: Thông tin test + tên giáo viên

#### 3.2.2 Chi Tiết Test

- **Endpoint**: `GET /bai-tests/{id}`
- **Middleware**: `auth:sanctum, role:hoc_sinh`
- **Return**:
    - Thông tin test
    - Danh sách câu hỏi (CauHois)
    - Danh sách đáp án (DapAns) cho mỗi câu

#### 3.2.3 Tạo Test Mới

- **Endpoint**: `POST /bai-tests`
- **Middleware**: `auth:sanctum, role:giao_vien`
- **Dữ liệu nhập**:
    - `id_lesson`: ID bài học (required)
    - `ten_bai_test`: Tên test (required)
    - `mo_ta`: Mô tả test
    - `thoi_gian_toi_da`: Thời gian tối đa (phút)
    - `diem_tong_max`: Điểm tối đa
    - `trang_thai`: 1=draft, 2=published
- **Auto set**: `id_giao_vien = auth()->id()`

#### 3.2.4 Bắt Đầu Làm Test

- **Endpoint**: `POST /bai-tests/{testId}/start`
- **Middleware**: `auth:sanctum, role:hoc_sinh`
- **Kiểm tra**:
    - Test phải published
    - Học sinh phải enrolled khóa học
    - Tạo `StudentTestResult` để tracking
    - Lập tinh thời gian bắt đầu

#### 3.2.5 Nộp Bài

- **Endpoint**: `POST /bai-tests/{testId}/submit`
- **Middleware**: `auth:sanctum, role:hoc_sinh`
- **Chức năng**:
    - Kiểm tra thời gian hết hạn
    - Lưu câu trả lời của học sinh
    - Tính điểm tự động (multiple choice)
    - Cập nhật trạng thái test result

#### 3.2.6 Xem Kết Quả

- **Endpoint**: `GET /bai-tests/{testId}/result`
- **Middleware**: `auth:sanctum, role:hoc_sinh`
- **Return**:
    - Tổng điểm
    - Thời gian làm bài
    - Các câu trả lời của học sinh
    - Đáp án đúng

#### 3.2.7 Quản Lý Câu Hỏi

- **Endpoints**: CRUD câu hỏi
- **Middleware**: `auth:sanctum, role:giao_vien`
- **Dữ liệu**:
    - `noi_dung`: Nội dung câu hỏi
    - `loai_cau_hoi`: loại (multiple_choice, essay, etc.)
    - `diem_max`: Điểm tối đa
    - `thu_tu`: Thứ tự câu hỏi
- **Quyền**: Chỉ giáo viên sở hữu test mới được edit

#### 3.2.8 Quản Lý Đáp Án

- **Endpoints**: CRUD đáp án
- **Middleware**: `auth:sanctum, role:giao_vien`
- **Dữ liệu**:
    - `noi_dung`: Nội dung đáp án
    - `la_dap_an_dung`: Đáp án đúng? (boolean)
    - `thu_tu`: Thứ tự đáp án
- **Quyền**: Chỉ giáo viên sở hữu test mới được edit

### 3.3 **⚠️ Chức Năng Đề Thi Còn Thiếu**

| Chức Năng                          | Trạng Thái | Ghi Chú                              |
| ---------------------------------- | ---------- | ------------------------------------ |
| ✓ CRUD cơ bản                      | ✅         | Đã có                                |
| ✗ Bộ lọc/Tìm kiếm test             | THIẾU      | Theo tên, giáo viên, bài học         |
| ✗ Phân trang test                  | THIẾU      | Khi có nhiều test                    |
| ✗ Nhập/Xuất câu hỏi (CSV/Excel)    | THIẾU      | Bulk import questions                |
| ✗ Sao chép test (Clone test)       | THIẾU      | Tạo test mới từ test cũ              |
| ✗ Hạn chế thời gian làm bài        | THIẾU      | Enforce time limit                   |
| ✗ Xáo trộn câu hỏi (Shuffle)       | THIẾU      | Random order per student             |
| ✗ Xáo trộn đáp án                  | THIẾU      | Random order per student             |
| ✗ Loại câu hỏi: Essay/Essay review | THIẾU      | Giáo viên chấm tay                   |
| ✗ Loại câu hỏi: Matching           | THIẾU      | Nối cặp câu-đáp án                   |
| ✗ Loại câu hỏi: Fill in blank      | THIẾU      | Điền từ còn thiếu                    |
| ✗ Loại câu hỏi: Image/Audio        | THIẾU      | Media support                        |
| ✗ Tính điểm tự động (auto-grade)   | THIẾU      | Chỉ có cơ bản multiple choice        |
| ✗ Thêm ghi chú cho câu hỏi         | THIẾU      | Giáo viên add explanation            |
| ✗ Dashboard phân tích test         | THIẾU      | Stats per question, difficulty level |
| ✗ Giới hạn lượng lần làm test      | THIẾU      | Chỉ cho làm 1 lần, 2 lần, etc        |
| ✗ Cho phép làm lại (Retake)        | THIẾU      | Workflow retry                       |
| ✗ Review test sau khi submit       | THIẾU      | Học sinh xem lại đáp án              |
| ✗ Ghi nhận thời gian submit        | THIẾU      | Deadline enforcement                 |
| ✗ Mã hóa/Bảo mật câu hỏi           | THIẾU      | Đề thi ẩn cho đến khi bắt đầu        |

---

## 4️⃣ THỐNG KÊ & PHÂN TÍCH (Analytics)

### 4.1 **Hiện Tại**

- ✓ Thống kê cơ bản: Tổng lessons, tests, teachers, students

### 4.2 **Cần Thêm**

- ✗ Thống kê hoạt động theo ngày/tuần/tháng
- ✗ Tỷ lệ thành công của test (pass rate)
- ✗ Mức độ khó của câu hỏi (basado on student performance)
- ✗ Thời gian trung bình hoàn thành test
- ✗ Ghi nhận ai đã làm test, ai chưa
- ✗ Report: Giáo viên xem kết quả học sinh theo class
- ✗ Export report (PDF, Excel)

---

## 5️⃣ SECURITY & AUTHORIZATION (Bảo mật & Phân quyền)

### 5.1 **Hiện Tại**

- ✓ Middleware role-based: admin, giao_vien, hoc_sinh
- ✓ API token authentication (Sanctum)
- ✓ Kiểm tra ownership (giáo viên chỉ edit bài của mình)

### 5.2 **Cần Thêm**

- ✗ Rate limiting chi tiết hơn
- ✗ Audit log: Ghi nhận ai thay đổi gì, khi nào
- ✗ CORS configuration
- ✗ HTTPS-only in production
- ✗ SQL Injection protection (đã có qua ORM)
- ✗ XSS protection
- ✗ CSRF token

---

## 6️⃣ DATABASE MODELS (Mô Hình Dữ Liệu)

### 6.1 **Hiện Tại**

```
Users (id, name, email, password, role, is_active)
  ├─ Lessons (id, id_giao_vien, tieu_de, mo_ta, noi_dung, trang_thai)
  ├─ BaiTests (id, id_giao_vien, id_lesson, ten_bai_test, mo_ta, thoi_gian_toi_da, diem_tong_max, trang_thai)
  │   ├─ CauHois (id, id_bai_test, noi_dung, loai_cau_hoi, thu_tu, diem_max)
  │   │   └─ DapAns (id, id_cau_hoi, noi_dung, la_dap_an_dung, thu_tu)
  │   └─ StudentTestResults (id, id_hoc_sinh, id_bai_test, diem, trang_thai)
  │       └─ StudentAnswerDetails (id, id_student_test_result, id_cau_hoi, response)
  ├─ CourseEnrollments (id_hoc_sinh, id_lesson)
  └─ LessonProgresses (id_hoc_sinh, id_lesson, progress)
```

### 6.2 **Cần Thêm**

- ✗ AuditLogs table: Ghi nhận tất cả thay đổi
- ✗ Categories/Topics table: Phân loại bài học
- ✗ UserActivity table: Tracking user behavior
- ✗ TestResultDetails: Chi tiết điểm từng câu
- ✗ QuestionBankV2: Thư viện câu hỏi tái sử dụng

---

## 7️⃣ FRONTEND (Vue.js) - CẦN KIỂM TRA

Cần xem frontend để hiểu:

- ✓ Làm được gì từ UI hiện tại?
- ✗ Có những chức năng nào backend hỗ trợ nhưng frontend chưa?
- ✗ Cần thêm page/component nào?

---

## 8️⃣ KHUYẾN NGHỊ ƯUTIÊN (Priority Recommendations)

### **Phase 1: Thiết Yếu (Must Have)**

🟠 **P1.1** - Thêm API quản lý user cho Admin:

- `GET /admin/users` - Liệt kê toàn bộ user
- `GET /admin/users/{id}` - Chi tiết user
- `PUT /admin/users/{id}` - Cập nhật (vô hiệu/kích hoạt)
- `DELETE /admin/users/{id}` - Xóa user

🟠 **P1.2** - Phân trang cho danh sách:

- Lesson index
- BaiTest index
- User list

🟠 **P1.3** - Tìm kiếm/Filter:

- Lọc test theo status
- Tìm kiếm lesson theo tên
- Tìm kiếm user

### **Phase 2: Quan Trọng (Should Have)**

⚡ **P2.1** - Loại câu hỏi mở rộng:

- Essay/Subjective (giáo viên chấm)
- Matching
- Fill-in-blank

⚡ **P2.2** - Xáo trộn câu hỏi & đáp án

⚡ **P2.3** - Thống kê chi tiết test (analytics dashboard)

⚡ **P2.4** - Audit log system

### **Phase 3: Nice to Have**

💡 **P3.1** - Sao chép test/Nhập câu hỏi từ file

💡 **P3.2** - Export kết quả (PDF/Excel)

💡 **P3.3** - Ghi chú/Explanation cho câu hỏi

💡 **P3.4** - Activity log chi tiết

---

## 9️⃣ VALIDATION & ERROR HANDLING

### **Cần Kiểm Tra**

- ✗ Custom validation messages (tiếng Việt)
- ✗ Input sanitization
- ✗ Rate limiting per endpoint
- ✗ Detailed error messages vs generic

---

## 🔟 TESTING COVERAGE

### **Cần Thêm**

- ✗ Unit tests cho controllers
- ✗ Feature tests cho API flows
- ✗ Authorization tests
- ✗ Data validation tests

---

## 📊 TÓMLẠO TỔNG HỢP

| Danh Mục      | Hoàn thành | Còn thiếu | Tỷ lệ   |
| ------------- | ---------- | --------- | ------- |
| **Admin**     | 2          | 8         | 20%     |
| **Lesson**    | 6          | 9         | 40%     |
| **BaiTest**   | 8          | 12        | 40%     |
| **Security**  | 3          | 7         | 30%     |
| **Analytics** | 1          | 6         | 15%     |
| **TOTAL**     | 20         | 42        | **32%** |

---

## 📝 GƯỜI VIẾT & CẬP NHẬT

- **Ngày phân tích**: 13/03/2026
- **Phiên bản**: 1.0
- **Trạng thái**: Initial Assessment

---

**Tiếp theo?** Xác nhận các yêu cầu ưu tiên và bắt đầu development Phase 1! 🚀
