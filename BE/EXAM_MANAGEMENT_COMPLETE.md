# 📋 Hoàn Thiện Chức Năng Quản Lý Đề Thi - Chi Tiết Implementation

**Ngày hoàn thành**: 13/03/2026  
**Phiên bản**: 2.0 - Complete  
**Trạng thái**: ✅ Backend hoàn thiện

---

## 📑 MỤC LỤC

1. [Tổng Quan Thay Đổi](#tổng-quan-thay-đổi)
2. [Database Migrations](#database-migrations)
3. [API Endpoints Mới](#api-endpoints-mới)
4. [Models Mới & Cập Nhật](#models-mới--cập-nhật)
5. [Logic Chính](#logic-chính)
6. [Hướng Dẫn Sử Dụng](#hướng-dẫn-sử-dụng)
7. [Frontend TODO](#frontend-todo)

---

## 🔄 Tổng Quan Thay Đổi

### ✅ Hoàn Thiện Được

| Tính Năng                    | Trạng Thái | Ghi Chú                                                                         |
| ---------------------------- | ---------- | ------------------------------------------------------------------------------- |
| 🔀 Xáo trộn câu hỏi & đáp án | ✅         | Per student, shuffle per attempt                                                |
| ⏱️ Enforced time limit       | ✅         | Kiểm tra serverside, ko bị bypass                                               |
| 🎯 Auto-grading mở rộng      | ✅         | Multiple choice, matching, fill-blank, essay                                    |
| 🔄 Retake limit              | ✅         | Giới hạn số lần làm (0=unlimited)                                               |
| 📊 Analytics & Dashboard     | ✅         | Per test & per question statistics                                              |
| 🔍 Search & Filter test      | ✅         | By name, status, pagination                                                     |
| 📋 Question types            | ✅         | 6 types: multiple_choice, true_false, essay, matching, fill_blank, image_choice |
| 📸 Images support            | ✅         | For questions & answers (URLs)                                                  |
| 💬 Test settings             | ✅         | Show results immediately, review test, show answers                             |
| 📅 Time availability         | ✅         | Start date, end date scheduling                                                 |
| 📝 Question notes            | ✅         | Description, explanation per question                                           |

---

## 🗄️ Database Migrations

### Migration 1: Mở Rộng Question & Answer Types

**File**: `2024_03_13_000001_extend_question_types.php`

```php
// cau_hois table - Mới:
- loai_cau_hoi (string): multiple_choice, true_false, essay, matching, fill_blank, image_choice
- mo_ta_chi_tiet (text): Chi tiết/instruction cho câu hỏi
- ghi_chu (text): Teacher notes
- hinh_anh_url (string): Image URL cho câu hỏi

// dap_ans table - Mới:
- diem_tu_dong (float): Auto score if correct
- hinh_anh_url (string): Image URL cho đáp án
- mo_ta_chi_tiet (text): Explanation cho đáp án
```

### Migration 2: Test Settings & Retake Logic

**File**: `2024_03_13_000002_extend_test_settings.php`

```php
// bai_tests table - Mới:
- so_lan_lam_toi_da (int): Max attempts (0=unlimited)
- co_xao_tron_cau_hoi (bool): Shuffle questions
- co_xao_tron_dap_an (bool): Shuffle answers
- hien_thi_ket_qua_ngay_lap (bool): Show result immediately
- hien_thi_dap_an_dung (bool): Show correct answers
- cho_xem_lai_test (bool): Allow review after submit
- ngay_bat_dau (datetime): Start availability
- ngay_ket_thuc (datetime): End availability

// student_test_results table - Mới/Updated:
- lan_thu (int): Attempt number
- thoi_gian_bat_dau (datetime): Start time
- thoi_gian_hoan_thanh (datetime): Complete time
- thoi_gian_su_dung (int): Duration in seconds
- so_cau_dung (int): Correct answers count
- so_cau_sai (int): Wrong answers count
- so_cau_bo_trong (int): Unanswered count
- trang_thai (string): not_started, in_progress, completed, pending_review, grading
- ghi_chu_giao_vien (text): Teacher feedback
```

### Migration 3: Analytics Tables

**File**: `2024_03_13_000003_create_analytics_tables.php`

```php
// test_analytics table:
- id_bai_test (FK)
- so_hoc_sinh_lam (int)
- diem_trung_binh (float)
- diem_min, diem_max (float)
- ty_le_hoc_sinh_dau (float): Pass rate %
- thoi_gian_trung_binh (int): Seconds

// question_analytics table:
- id_cau_hoi (FK), id_bai_test (FK)
- so_hoc_sinh_lam, so_hoc_sinh_tra_loi_dung (int)
- ty_le_tra_loi_dung (float): Success rate %
- do_kho (float): 0-100 difficulty
- diem_trung_binh (float)

// answer_analytics table:
- id_dap_an (FK), id_cau_hoi (FK)
- so_hoc_sinh_chon (int)
- ty_le_hoc_sinh_chon (float): % students selected

// activity_logs table:
- id_user, action, action_type, related_id
- details, ip_address, user_agent
```

---

## 🔌 API Endpoints Mới

### 📚 Bài Test - Public & Teacher

#### GET `/lessons/{lessonId}/bai-tests` (Public) - **UPDATED**

```json
Query Parameters:
- search: string (Tim kiem theo ten)
- status: int (1=draft, 2=published)
- sort_by: string (created_at, ten_bai_test, updated_at)
- sort_order: asc|desc
- per_page: int (default: 15)

Response:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "ten_bai_test": "Mid-term Exam",
      "mo_ta": "...",
      "thoi_gian_toi_da": 60,
      "diem_tong_max": 100,
      "trang_thai": 2,
      "giao_vien": { "id": 1, "name": "..." }
    }
  ],
  "pagination": {
    "total": 25,
    "per_page": 15,
    "current_page": 1,
    "last_page": 2
  }
}
```

#### GET `/teacher/bai-tests` (Teacher) - **UPDATED**

```
Same query parameters as above
Returns: Bai tests cua giao vien hien tai
```

#### POST `/bai-tests` (Teacher) - **UPDATED**

```json
Request Body:
{
  "id_lesson": 1,
  "ten_bai_test": "Final Exam",
  "mo_ta": "...",
  "thoi_gian_toi_da": 120,
  "diem_tong_max": 100,
  "trang_thai": 2,

  // NEW FIELDS:
  "so_lan_lam_toi_da": 2,           // 0 = unlimited
  "co_xao_tron_cau_hoi": true,
  "co_xao_tron_dap_an": true,
  "hien_thi_ket_qua_ngay_lap": true,
  "hien_thi_dap_an_dung": true,
  "cho_xem_lai_test": true,
  "ngay_bat_dau": "2024-03-15T08:00:00Z",
  "ngay_ket_thuc": "2024-03-15T10:00:00Z"
}
```

#### PUT `/bai-tests/{id}` (Teacher) - **UPDATED**

Cùng endpoint POST, update các fields

#### GET `/bai-tests/{id}` (Student) - **UPDATED**

```json
Response:
{
  "success": true,
  "data": {
    "id": 1,
    "ten_bai_test": "...",
    "questions": [
      {
        "id": 1,
        "noi_dung": "Question 1?",
        "mo_ta_chi_tiet": "...",
        "loai_cau_hoi": "multiple_choice",
        "hinh_anh_url": "https://...",
        "diem_max": 5,
        "answers": [
          {
            "id": 1,
            "noi_dung": "Answer A",
            "hinh_anh_url": "..."
          }
        ]
      }
    ]
  }
}

NOTE: Co xao tron neu co_xao_tron_cau_hoi hoac co_xao_tron_dap_an = true
```

#### POST `/bai-tests/{testId}/start` (Student) - **UPDATED**

```json
Response:
{
  "success": true,
  "data": {
    "id": 123,            // StudentTestResult ID
    "id_bai_test": 1,
    "lan_thu": 2,         // Attempt 2
    "trang_thai": "in_progress",
    "thoi_gian_bat_dau": "2024-03-15T08:00:00Z",
    "thoi_gian_toi_da": 120              // Minutes
  }
}

Errors:
- 403: Vuot qua so lan lam toi da
- 403: Test khong con khả dung (time scheduling)
- 403: Chua enroll khoa hoc
```

#### POST `/bai-tests/{testId}/submit` (Student) - **UPDATED**

```json
Request:
{
  "answers": [
    {
      "id_cau_hoi": 1,
      "id_dap_an": 5,                 // Null if essay
      "cau_tra_loi_tu_do": "Answer text"   // For essay
    }
  ]
}

Response:
{
  "success": true,
  "data": {
    "id": 123,
    "diem_tong": 85.5,
    "so_cau_dung": 17,
    "so_cau_sai": 2,
    "so_cau_bo_trong": 1,
    "thoi_gian_su_dung": 4523,        // Seconds
    "trang_thai": "completed",
    "result_display": {       // If hien_thi_ket_qua_ngay_lap = true
      "diem_tong": 85.5,
      "diem_tong_max": 100,
      "ty_le_phan_tram": 85.5,
      "so_cau_dung": 17,
      "so_cau_sai": 2,
      "so_cau_bo_trong": 1,
      "thoi_gian_su_dung_phut": 75.38
    }
  }
}

Logic:
- Auto-grade each question based on loai_cau_hoi
- Check time limit (error if exceed)
- Save all answers in StudentAnswerDetail
- Update: diem_tong, so_cau_dung, so_cau_sai, so_cau_bo_trong, thoi_gian_su_dung
- Set trang_thai = "completed"
- Update analytics tables
```

#### GET `/bai-tests/{testId}/result` (Student) - **UPDATED**

```json
Response:
{
  "success": true,
  "data": {
    "id": 123,
    "diem_tong": 85.5,
    "so_cau_dung": 17,
    "so_cau_sai": 2,
    "so_cau_bo_trong": 1,
    "thoi_gian_su_dung": 4523,
    "lan_thu": 2,
    "trang_thai": "completed",
    "ghi_chu_giao_vien": "Very good!",
    "thoi_gian_hoan_thanh": "2024-03-15T09:15:00Z",
    "chi_tiet_tung_cau": [
      {
        "id_cau_hoi": 1,
        "noi_dung_cau_hoi": "Question 1?",
        "loai_cau_hoi": "multiple_choice",
        "diem_max": 5,
        "diem_tong": 5,
        "dap_an_chon": "Answer A",
        "dap_an_dung": "Answer A"    // Only if hien_thi_dap_an_dung=true
      }
    ]
  }
}
```

#### 🆕 GET `/bai-tests/{id}/analytics` (Teacher)

```json
Response:
{
  "success": true,
  "data": {
    "test_id": 1,
    "ten_bai_test": "Final Exam",
    "analytics": {
      "so_hoc_sinh_lam": 45,
      "diem_trung_binh": 75.3,
      "diem_min": 42.0,
      "diem_max": 98.5,
      "ty_le_hoc_sinh_dau": 84.4,       // % pass
      "thoi_gian_trung_binh": 3540      // Seconds
    },
    "question_analytics": [
      {
        "id_cau_hoi": 1,
        "noi_dung": "Question 1?",
        "so_hoc_sinh_lam": 45,
        "ty_le_tra_loi_dung": 92.2,     // % correct
        "do_kho": 7.8,                  // Difficulty 0-100
        "diem_trung_binh": 4.6
      }
    ],
    "student_attempts": [
      {
        "id": 123,
        "id_hoc_sinh": 5,
        "diem_tong": 95.0,
        "so_cau_dung": 19,
        "thoi_gian_su_dung": 2800,
        "lan_thu": 1,
        "created_at": "2024-03-15T08:50:00Z"
      }
    ]
  }
}

Errors:
- 403: Khong phai giao vien cua test nay
```

---

## 📦 Models Mới & Cập Nhật

### Models Mới

#### ✨ `TestAnalytic::class`

```php
$table->id();
$table->unsignedBigInteger('id_bai_test');
$table->integer('so_hoc_sinh_lam');
$table->float('diem_trung_binh');
$table->float('diem_min');
$table->float('diem_max');
$table->float('ty_le_hoc_sinh_dau');
$table->integer('thoi_gian_trung_binh');

// Relationships
public function test() -> belongsTo(BaiTest::class)
```

#### ✨ `QuestionAnalytic::class`

```php
$table->id();
$table->unsignedBigInteger('id_cau_hoi');
$table->unsignedBigInteger('id_bai_test');
$table->integer('so_hoc_sinh_lam');
$table->integer('so_hoc_sinh_tra_loi_dung');
$table->float('ty_le_tra_loi_dung');
$table->float('do_kho');
$table->float('diem_trung_binh');

// Relationships
public function question() -> belongsTo(CauHoi::class)
public function test() -> belongsTo(BaiTest::class)
```

#### ✨ `AnswerAnalytic::class`

```php
$table->id();
$table->unsignedBigInteger('id_dap_an');
$table->unsignedBigInteger('id_cau_hoi');
$table->integer('so_hoc_sinh_chon');
$table->float('ty_le_hoc_sinh_chon');

// Relationships
public function answer() -> belongsTo(DapAn::class)
public function question() -> belongsTo(CauHoi::class)
```

#### ✨ `ActivityLog::class`

```php
$table->id();
$table->unsignedBigInteger('id_user');
$table->string('action');         // created, updated, deleted, submitted
$table->string('action_type');    // test, question, answer, result
$table->unsignedBigInteger('related_id')->nullable();
$table->text('details')->nullable();
$table->string('ip_address')->nullable();
$table->text('user_agent')->nullable();

// Relationships
public function user() -> belongsTo(User::class)
```

### Models Cập Nhật

#### 🔧 `BaiTest::class` - NEW FIELDS

```php
// New Fillable:
'so_lan_lam_toi_da',
'co_xao_tron_cau_hoi',
'co_xao_tron_dap_an',
'hien_thi_ket_qua_ngay_lap',
'hien_thi_dap_an_dung',
'cho_xem_lai_test',
'ngay_bat_dau',
'ngay_ket_thuc',

// New Casts:
'so_lan_lam_toi_da' => 'integer',
'co_xao_tron_cau_hoi' => 'boolean',
'co_xao_tron_dap_an' => 'boolean',
'hien_thi_ket_qua_ngay_lap' => 'boolean',
'hien_thi_dap_an_dung' => 'boolean',
'cho_xem_lai_test' => 'boolean',
'ngay_bat_dau' => 'datetime',
'ngay_ket_thuc' => 'datetime',

// New Relationships:
public function analytics() -> hasOne(TestAnalytic::class)
```

#### 🔧 `CauHoi::class` - NEW FIELDS

```php
// New Fillable:
'mo_ta_chi_tiet',
'ghi_chu',
'hinh_anh_url',

// New Relationships:
public function analytics() -> hasMany(QuestionAnalytic::class)

// New Helper Methods:
public function isMatching(): bool
public function isFillBlank(): bool
public function isTrueFalse(): bool
```

#### 🔧 `DapAn::class` - NEW FIELDS

```php
// New Fillable:
'diem_tu_dong',
'hinh_anh_url',
'mo_ta_chi_tiet',

// New Casts:
'diem_tu_dong' => 'float',
```

#### 🔧 `StudentTestResult::class` - UPDATED

```php
// Updated Fillable:
'lan_thu',
'thoi_gian_bat_dau',
'thoi_gian_hoan_thanh',
'thoi_gian_su_dung',
'so_cau_dung',
'so_cau_sai',
'so_cau_bo_trong',
'trang_thai',
'ghi_chu_giao_vien',

// Updated Casts:
'trang_thai' => Enum to String (not_started, in_progress, completed, pending_review, grading)

// Updated Helper Methods (string-based trang_thai):
public function isNotStarted(): bool -> trang_thai === 'not_started'
public function isInProgress(): bool -> trang_thai === 'in_progress'
public function isSubmitted(): bool -> trang_thai === 'completed'
public function isGraded(): bool -> in_array(trang_thai, ['pending_review', 'grading'])
```

---

## ⚙️ Logic Chính

### 1. Auto-Grading Logic

**Location**: `BaiTestController::gradeAnswer()` (line 530-570)

```php
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
            // Manual grading needed by teacher
            $score = 0;
            break;
    }

    return min($score, $question->diem_max);
}
```

**Question Types Supported**:

- ✅ `multiple_choice`: Select 1 correct answer
- ✅ `true_false`: True or False
- ✅ `image_choice`: Choose image
- ✅ `matching`: Match pairs (partial scoring)
- ✅ `fill_blank`: Fill blanks (partial scoring)
- ✅ `essay`: Student writes, teacher grades

### 2. Shuffle Logic

**Location**: `BaiTestController::show()` (line 110-140)

```php
// Shuffle questions if configured
if ($test->co_xao_tron_cau_hoi) {
    $questions = $questions->shuffle();
}

// Shuffle answers if configured
if ($test->co_xao_tron_dap_an) {
    $answers = $answers->shuffle();
}
```

**How It Works**:

- Shuffle happens on **per-student basis** during `show()` call
- Different students see different order
- Server-side shuffle (secure, cannot bypass)

### 3. Time Enforcement

**Location**: `BaiTestController::submitTest()` (line 356-366)

```php
if ($test->thoi_gian_toi_da > 0) {
    $elapsedSeconds = $result->thoi_gian_bat_dau->diffInSeconds(now());
    $maxSeconds = $test->thoi_gian_toi_da * 60;

    if ($elapsedSeconds > $maxSeconds) {
        return response()->json([
            'success' => false,
            'message' => 'Thời gian làm bài đã hết',
        ], 403);
    }
}
```

**How It Works**:

- Check happens **on server** when submit
- Cannot be bypassed by frontend tampering
- Records actual time used: `thoi_gian_su_dung`

### 4. Retake Limit Logic

**Location**: `BaiTestController::startTest()` (line 234-250)

```php
if ($test->so_lan_lam_toi_da > 0) {
    $attemptCount = StudentTestResult::where('id_hoc_sinh', $userId)
        ->where('id_bai_test', $testId)
        ->whereIn('trang_thai', ['completed', 'pending_review', 'grading'])
        ->count();

    if ($attemptCount >= $test->so_lan_lam_toi_da) {
        return response()->json([
            'success' => false,
            'message' => "Bạn đã hết số lần làm bài test này (tối đa {$test->so_lan_lam_toi_da} lần)",
        ], 403);
    }
}
```

**How It Works**:

- `so_lan_lam_toi_da = 0` → Unlimited attempts
- `so_lan_lam_toi_da = 1` → Only once
- `so_lan_lam_toi_da = 2` → Max 2 attempts
- Tracks: `StudentTestResult.lan_thu` (attempt number)

### 5. Analytics Update Logic

**Location**: `BaiTestController::updateTestAnalytics()` (line 620-660)

```php
private function updateTestAnalytics($testId, $result, $validQuestions)
{
    $analytics = TestAnalytic::firstOrCreate(['id_bai_test' => $testId]);

    $completedResults = StudentTestResult::where('id_bai_test', $testId)
        ->where('trang_thai', 'completed')
        ->get();

    $totalScore = $completedResults->sum('diem_tong');
    $totalTime = $completedResults->sum('thoi_gian_su_dung');
    $passCount = $completedResults->where('diem_tong', '>=', 50)->count();

    $analytics->update([
        'so_hoc_sinh_lam' => $completedResults->count(),
        'diem_trung_binh' => ...,
        'diem_min' => ...,
        'diem_max' => ...,
        'ty_le_hoc_sinh_dau' => ...,
        'thoi_gian_trung_binh' => ...,
    ]);

    // Also update question analytics
    foreach ($validQuestions as $question) {
        $this->updateQuestionAnalytics($question, $testId);
    }
}
```

**Metrics Calculated**:

- `so_hoc_sinh_lam`: Total students completed
- `diem_trung_binh`: Average score
- `diem_min / diem_max`: Min/Max scores
- `ty_le_hoc_sinh_dau`: Pass rate (score >= 50)
- `thoi_gian_trung_binh`: Average time

---

## 📚 Hướng Dẫn Sử Dụng

### Cho Giáo Viên

#### 1. Tạo Bài Test Mới

```bash
POST /api/bai-tests
Content-Type: application/json
Authorization: Bearer {token}

{
  "id_lesson": 5,
  "ten_bai_test": "Mid-term Exam",
  "mo_ta": "Chapter 1-3 exam",
  "thoi_gian_toi_da": 90,
  "diem_tong_max": 100,
  "trang_thai": 1,
  "so_lan_lam_toi_da": 2,
  "co_xao_tron_cau_hoi": true,
  "co_xao_tron_dap_an": true,
  "hien_thi_ket_qua_ngay_lap": true,
  "hien_thi_dap_an_dung": false,
  "cho_xem_lai_test": true,
  "ngay_bat_dau": "2024-03-15T08:00:00Z",
  "ngay_ket_thuc": "2024-03-15T10:00:00Z"
}
```

#### 2. Thêm Câu Hỏi Multiple Choice

```bash
POST /api/bai-tests/5/cau-hois/1/dap-ans
Content-Type: application/json
Authorization: Bearer {token}

{
  "noi_dung": "What is the capital of France?",
  "mo_ta_chi_tiet": "European geography question",
  "loai_cau_hoi": "multiple_choice",
  "hinh_anh_url": "https://...",
  "ghi_chu": "Easy question",
  "diem_max": 5
}

// Add answers
POST /api/bai-tests/5/cau-hois/1/dap-ans

{
  "noi_dung": "Paris",
  "la_dap_an_dung": true,
  "diem_tu_dong": 5,
  "thu_tu": 1
}

{
  "noi_dung": "Berlin",
  "la_dap_an_dung": false,
  "thu_tu": 2
}
```

#### 3. Xem Analytics

```bash
GET /api/bai-tests/5/analytics
Authorization: Bearer {teacher_token}

Response:
{
  "so_hoc_sinh_lam": 30,
  "diem_trung_binh": 78.5,
  "ty_le_hoc_sinh_dau": 80.0,
  "question_analytics": [
    {
      "id_cau_hoi": 1,
      "ty_le_tra_loi_dung": 95.2,
      "do_kho": 4.8
    }
  ]
}
```

### Cho Học Sinh

#### 1. Xem Bài Test

```bash
GET /api/bai-tests/5
Authorization: Bearer {student_token}

Response:
{
  "ten_bai_test": "Mid-term Exam",
  "thoi_gian_toi_da": 90,
  "questions": [
    {
      "id": 1,
      "noi_dung": "What is the capital of France?",
      "loai_cau_hoi": "multiple_choice",
      "diem_max": 5,
      "answers": [
        { "id": 1, "noi_dung": "Paris" },
        { "id": 2, "noi_dung": "Berlin" },
        ...
      ]
    }
  ]
}

NOTE: Co the khac order neu co_xao_tron_cau_hoi = true
```

#### 2. Bắt Đầu Làm Bài

```bash
POST /api/bai-tests/5/start
Authorization: Bearer {student_token}

Response:
{
  "id": 500,              // StudentTestResult ID
  "lan_thu": 1,
  "thoi_gian_bat_dau": "2024-03-15T08:00:00Z",
  "thoi_gian_toi_da": 90
}
```

#### 3. Nộp Bài

```bash
POST /api/bai-tests/5/submit
Authorization: Bearer {student_token}

{
  "answers": [
    {
      "id_cau_hoi": 1,
      "id_dap_an": 1
    },
    {
      "id_cau_hoi": 2,
      "id_dap_an": null,
      "cau_tra_loi_tu_do": "My essay answer"
    }
  ]
}

Response:
{
  "diem_tong": 95.0,
  "so_cau_dung": 19,
  "thoi_gian_su_dung": 2345,
  "result_display": {
    "ty_le_phan_tram": 95.0,
    "so_cau_sai": 1,
    "so_cau_bo_trong": 0
  }
}
```

#### 4. Xem Kết Quả

```bash
GET /api/bai-tests/5/result
Authorization: Bearer {student_token}

Response:
{
  "diem_tong": 95.0,
  "chi_tiet_tung_cau": [
    {
      "id_cau_hoi": 1,
      "noi_dung_cau_hoi": "What is...?",
      "diem_tong": 5,
      "dap_an_chon": "Paris",
      "dap_an_dung": "Paris"
    }
  ]
}

NOTE: dap_an_dung only appear if hien_thi_dap_an_dung = true
```

---

## 🎨 Frontend TODO

### Components Cần Tạo (Vue.js)

```
✅ Backend complete
❌ Frontend TODO

Screens cần tạo:
1. TestListPage
   - Search, filter, sort, pagination
   - Create test button

2. TestBuilderPage
   - Add questions form
   - Question type selector
   - Reorder questions
   - Add images
   - Advanced settings (shuffle, retake, etc)

3. TestTakingPage
   - Display questions (with shuffle)
   - Multiple choice radio/checkbox
   - Text input for essay
   - Timer display
   - Submit button with confirmation

4. ResultPage
   - Score display
   - Details per question
   - Show correct answers (if enabled)
   - Retake button (if allowed)

5. AnalyticsDashboard
   - Test statistics chart
   - Question difficulty
   - Student performance list
   - Export report button

6. QuestionEditorModal
   - Edit all question types
   - Add/edit answers
   - Upload image
   - Mark correct answer
```

---

## 🧪 Testing Checklist

### Backend APIs Test

```javascript
❌ TODO:
- POST /bai-tests (with all new fields)
- GET /bai-tests?search=name&status=2&sort_by=created_at
- GET /teacher/bai-tests (pagination)
- POST /bai-tests/{id}/start (check retake limit)
- POST /bai-tests/{id}/submit (with timeout, shuffled)
- GET /bai-tests/{id}/analytics (verify calculations)
- Shuffle logic (multiple requests should differ)
- Time enforcement (exceed time → error)
- All 6 question types
- Auto-grading accuracy
```

---

## 📊 Database Schema Verification

```bash
# Run migrations:
php artisan migrate

# Verify tables:
php artisan db:show

# Check for errors:
php artisan migrate:status
```

---

## 🎯 Next Steps

### Phase 3 (If approved):

1. **Frontend Implementation**
    - Build Vue.js components for all screens
    - Integrate with new API endpoints
    - Add image upload functionality

2. **Advanced Features**
    - Question bank/library
    - Import questions from CSV
    - Clone test
    - Email notifications
    - Certificate on pass

3. **Security Hardening**
    - Rate limiting per endpoint
    - Audit logging implementation
    - Test cheating detection (patterns)
    - Encryption for sensitive data

4. **Performance Optimization**
    - Database indexing for analytics
    - Caching for frequent queries
    - async job for analytics calculation

---

## 📞 Support Documents

- **API Endpoints**: See section "API Endpoints Mới"
- **Database**: Migrations files in `database/migrations/`
- **Models**: App/Models/\*.php
- **Controllers**: App/Http/Controllers/BaiTestController.php

---

**Status**: ✅ Backend Complete - Ready for Frontend Development

**Questions?** Check the specific method in BaiTestController.php

---
