# API Testing Guide - English Learning Platform

## Quick Start

This guide provides curl commands and JSON payloads to test all refactored API endpoints.

---

## 1. Authentication

### 1.1 Register as Student (Public)

**Endpoint:** `POST /api/register`

```bash
curl -X POST http://localhost/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Nguyễn Văn A",
    "email": "student@example.com",
    "password": "Password123",
    "password_confirmation": "Password123"
  }'
```

**Expected Response (201):**

```json
{
    "success": true,
    "message": "Đăng ký thành công",
    "user": {
        "id": 1,
        "name": "Nguyễn Văn A",
        "email": "student@example.com",
        "role": "hoc_sinh"
    },
    "token": "1|abcdef..."
}
```

---

### 1.2 Login

**Endpoint:** `POST /api/login`

```bash
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "student@example.com",
    "password": "Password123"
  }'
```

**Expected Response (200):**

```json
{
    "success": true,
    "message": "Đăng nhập thành công",
    "user": {
        "id": 1,
        "name": "Nguyễn Văn A",
        "email": "student@example.com",
        "role": "hoc_sinh"
    },
    "token": "2|xyz123..."
}
```

Save the `token` for subsequent requests.

---

### 1.3 Get Current User

**Endpoint:** `GET /api/auth/me`

```bash
curl -X GET http://localhost/api/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Expected Response (200):**

```json
{
    "success": true,
    "user": {
        "id": 1,
        "name": "Nguyễn Văn A",
        "email": "student@example.com",
        "role": "hoc_sinh",
        "is_active": true,
        "created_at": "2026-01-13T10:00:00.000000Z"
    }
}
```

---

### 1.4 Logout

**Endpoint:** `POST /api/logout`

```bash
curl -X POST http://localhost/api/logout \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Expected Response (200):**

```json
{
    "success": true,
    "message": "Đăng xuất thành công"
}
```

---

## 2. Teacher Management (Admin Only)

### 2.1 Create Teacher Account

**Endpoint:** `POST /api/admin/teachers`

**Note:** Only admin users can access this. First, create an admin manually in DB or seed.

```bash
curl -X POST http://localhost/api/admin/teachers \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -d '{
    "name": "Trần Thị B",
    "email": "teacher@example.com",
    "password": "TeacherPass123"
  }'
```

**Expected Response (201):**

```json
{
    "success": true,
    "message": "Tạo giáo viên thành công",
    "data": {
        "id": 2,
        "name": "Trần Thị B",
        "email": "teacher@example.com",
        "role": "giao_vien"
    }
}
```

---

## 3. Lessons (Teacher)

### 3.1 Create Lesson

**Endpoint:** `POST /api/lessons`

**Role Required:** `giao_vien`

```bash
curl -X POST http://localhost/api/lessons \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TEACHER_TOKEN" \
  -d '{
    "tieu_de": "Unit 1: Basic English",
    "mo_ta": "Introduction to English language",
    "noi_dung": "This lesson covers basic English vocabulary and grammar...",
    "trang_thai": 1
  }'
```

**Expected Response (201):**

```json
{
    "success": true,
    "message": "Tạo bài học thành công",
    "data": {
        "id": 1,
        "tieu_de": "Unit 1: Basic English",
        "mo_ta": "Introduction to English language",
        "trang_thai": 1
    }
}
```

**Note:** `trang_thai=1` is draft, `trang_thai=2` is published.

---

### 3.2 Update Lesson

**Endpoint:** `PUT /api/lessons/{id}`

```bash
curl -X PUT http://localhost/api/lessons/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TEACHER_TOKEN" \
  -d '{
    "tieu_de": "Unit 1: Basic English (Updated)",
    "mo_ta": "Introduction to English language",
    "noi_dung": "Updated content...",
    "trang_thai": 2
  }'
```

---

### 3.3 Get All Lessons (Public)

**Endpoint:** `GET /api/lessons`

```bash
curl -X GET http://localhost/api/lessons
```

**Expected Response (200):**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "tieu_de": "Unit 1: Basic English",
            "mo_ta": "Introduction to English language",
            "giao_vien": {
                "id": 2,
                "name": "Trần Thị B"
            },
            "created_at": "2026-01-13T10:05:00.000000Z"
        }
    ]
}
```

---

### 3.4 Get My Lessons (Teacher)

**Endpoint:** `GET /api/teacher/lessons`

```bash
curl -X GET http://localhost/api/teacher/lessons \
  -H "Authorization: Bearer TEACHER_TOKEN"
```

---

## 4. Tests/Quizzes (Teacher)

### 4.1 Create Test

**Endpoint:** `POST /api/bai-tests`

```bash
curl -X POST http://localhost/api/bai-tests \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TEACHER_TOKEN" \
  -d '{
    "id_lesson": 1,
    "ten_bai_test": "Unit 1 Quiz",
    "mo_ta": "Test your knowledge of Unit 1",
    "thoi_gian_toi_da": 30,
    "diem_tong_max": 10,
    "trang_thai": 2
  }'
```

**Expected Response (201):**

```json
{
    "success": true,
    "message": "Tạo bài test thành công",
    "data": {
        "id": 1,
        "ten_bai_test": "Unit 1 Quiz",
        "thoi_gian_toi_da": 30,
        "diem_tong_max": 10
    }
}
```

---

### 4.2 Get Tests for Lesson (Public)

**Endpoint:** `GET /api/lessons/{lessonId}/bai-tests`

```bash
curl -X GET http://localhost/api/lessons/1/bai-tests
```

---

### 4.3 Get Test Details (Public)

**Endpoint:** `GET /api/bai-tests/{id}`

```bash
curl -X GET http://localhost/api/bai-tests/1
```

**Expected Response (200):**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "ten_bai_test": "Unit 1 Quiz",
        "mo_ta": "Test your knowledge of Unit 1",
        "thoi_gian_toi_da": 30,
        "diem_tong_max": 10,
        "giao_vien": {
            "id": 2,
            "name": "Trần Thị B"
        },
        "cau_hois": [
            {
                "id": 1,
                "noi_dung": "What is your name?",
                "loai_cau_hoi": 1,
                "diem_max": 1,
                "dap_ans": [
                    {
                        "id": 1,
                        "noi_dung": "My name is John",
                        "thu_tu": 1
                    },
                    {
                        "id": 2,
                        "noi_dung": "I am John",
                        "thu_tu": 2
                    }
                ]
            }
        ]
    }
}
```

---

## 5. Questions & Answers (Teacher)

### 5.1 Create Question via Raw Query

**Note:** No specific endpoint yet; create via database or extend controllers.

---

## 6. Student Test Submission (Student)

### 6.1 Start Test

**Endpoint:** `POST /api/bai-tests/{testId}/start`

```bash
curl -X POST http://localhost/api/bai-tests/1/start \
  -H "Authorization: Bearer STUDENT_TOKEN"
```

**Expected Response (200):**

```json
{
    "success": true,
    "message": "Bắt đầu làm bài test",
    "data": {
        "id": 1,
        "id_bai_test": 1,
        "trang_thai": 1
    }
}
```

---

### 6.2 Submit Test

**Endpoint:** `POST /api/bai-tests/{testId}/submit`

```bash
curl -X POST http://localhost/api/bai-tests/1/submit \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer STUDENT_TOKEN" \
  -d '{
    "answers": [
      {
        "id_cau_hoi": 1,
        "id_dap_an": 2,
        "cau_tra_loi_tu_do": null
      },
      {
        "id_cau_hoi": 2,
        "id_dap_an": null,
        "cau_tra_loi_tu_do": "My answer to the essay question"
      }
    ]
  }'
```

**Expected Response (200):**

```json
{
    "success": true,
    "message": "Nộp bài test thành công",
    "data": {
        "id": 1,
        "diem_tong": 5.5,
        "trang_thai": 2
    }
}
```

**Note:**

-   `diem_tong` is auto-calculated by the Observer
-   Essay answers (null `id_dap_an`) are not scored automatically

---

### 6.3 Get Test Result

**Endpoint:** `GET /api/bai-tests/{testId}/result`

```bash
curl -X GET http://localhost/api/bai-tests/1/result \
  -H "Authorization: Bearer STUDENT_TOKEN"
```

**Expected Response (200):**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "diem_tong": 5.5,
        "trang_thai": 2,
        "thoi_gian_ket_thuc": "2026-01-13T10:30:00.000000Z",
        "chi_tiet_tung_cau": [
            {
                "id_cau_hoi": 1,
                "noi_dung_cau_hoi": "What is your name?",
                "dap_an_chon": "I am John",
                "diem_cau_hoi": 1,
                "la_dung": true
            },
            {
                "id_cau_hoi": 2,
                "noi_dung_cau_hoi": "Describe yourself",
                "dap_an_chon": "My answer to the essay question",
                "diem_cau_hoi": null,
                "la_dung": null
            }
        ]
    }
}
```

---

## 7. Authorization Testing

### 7.1 Test Role Access Control

**Scenario:** Student tries to create a lesson (should fail)

```bash
curl -X POST http://localhost/api/lessons \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer STUDENT_TOKEN" \
  -d '{"tieu_de": "..."}'
```

**Expected Response (403):**

```json
{
    "message": "Forbidden - Insufficient permissions"
}
```

---

### 7.2 Test Unauthenticated Access

```bash
curl -X GET http://localhost/api/teacher/lessons
```

**Expected Response (401):**

```json
{
    "message": "Unauthorized"
}
```

---

## 8. Scoring Logic Test

### Expected Behavior

**Single Choice / Multiple Choice:**

-   If answer is correct → Full points
-   If answer is incorrect → 0 points
-   If no answer selected → 0 points

**Essay Questions:**

-   Score remains `null` until teacher grades
-   `la_dung` remains `null`

**Total Score:**

-   Auto-calculated on submission
-   Recalculated when any answer's score changes
-   Rounded to 2 decimal places

---

## Common Issues & Solutions

| Issue                             | Solution                                                       |
| --------------------------------- | -------------------------------------------------------------- |
| "Unauthorized" on auth endpoint   | Check token is sent in `Authorization: Bearer` header          |
| "Forbidden" on teacher-only route | Check user role is `giao_vien`                                 |
| Empty lesson list                 | Create a lesson with `trang_thai=2` (published)                |
| Score not updating                | Ensure Observer is registered in AppServiceProvider            |
| Password mismatch                 | Confirm `password_confirmation` matches `password` in register |

---

## Setup for Manual Testing

### 1. Seed Initial Data (Optional)

Create a seeder to populate initial users and lessons:

```php
// database/seeders/TestDataSeeder.php
DB::table('users')->insert([
    ['name' => 'Admin User', 'email' => 'admin@test.com', 'password' => Hash::make('admin123'), 'role' => 'admin', 'is_active' => true],
    ['name' => 'Teacher User', 'email' => 'teacher@test.com', 'password' => Hash::make('teacher123'), 'role' => 'giao_vien', 'is_active' => true],
]);
```

Run: `php artisan db:seed --class=TestDataSeeder`

### 2. Use Postman Collection

Export this guide as Postman collection for easier testing.

### 3. Frontend Integration

Update Vue/Axios interceptors to include:

```javascript
axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
```

---

## Performance Notes

-   Tests with 100+ questions may take time to load
-   Submit response includes full scoring details (consider pagination for large results)
-   Implement caching for published lessons

---

_Last Updated: January 13, 2026_
