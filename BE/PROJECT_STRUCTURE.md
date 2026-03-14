# Project Structure - English Learning Platform Backend

```
BE_TiengAnh/
│
├── 📁 app/
│   ├── 📁 Http/
│   │   ├── 📁 Controllers/
│   │   │   ├── AuthController.php ........................ Authentication (register, login, logout, me)
│   │   │   ├── LessonController.php ..................... Lesson CRUD & retrieval
│   │   │   ├── BaiTestController.php ................... Test CRUD & submission
│   │   │   ├── AdminController.php ✨ NEW ............. Admin: Create teachers
│   │   │   ├── HomePageController.php ................. (Legacy, can remove)
│   │   │   └── Controller.php .......................... Base controller
│   │   │
│   │   ├── 📁 Middleware/
│   │   │   └── CheckRole.php ........................... Role-based access control middleware
│   │   │
│   │   └── 📁 Requests/ ................................ Form validation classes
│   │       ├── RegisterRequest.php ..................... Student registration validation
│   │       ├── LoginRequest.php ........................ Login validation
│   │       ├── StoreLessonRequest.php ................. Lesson creation/update validation
│   │       ├── StoreBaiTestRequest.php ............... Test creation/update validation
│   │       ├── SubmitTestRequest.php ................. Test submission validation
│   │       └── StoreTeacherRequest.php ✨ NEW ........ Teacher creation validation
│   │
│   ├── 📁 Models/ ....................................... Eloquent models
│   │   ├── User.php .................................... Base user model (auth, roles, relationships)
│   │   ├── GiaoVien.php ✨ NEW ........................ Teacher model (filtered User by role)
│   │   ├── HocSinh.php ✨ NEW ......................... Student model (filtered User by role)
│   │   ├── Lesson.php .................................. Course lesson
│   │   ├── BaiTest.php ................................. Quiz/test
│   │   ├── CauHoi.php .................................. Question
│   │   ├── DapAn.php ................................... Answer option
│   │   ├── StudentTestResult.php ....................... Quiz attempt
│   │   ├── StudentAnswerDetail.php ..................... Individual answer with auto-score
│   │   ├── CourseEnrollment.php ........................ Student enrollment in lesson
│   │   └── LessonProgress.php .......................... Lesson completion tracking
│   │
│   ├── 📁 Observers/
│   │   └── StudentAnswerDetailObserver.php 🔧 MODIFIED ... Auto-scoring logic
│   │
│   └── 📁 Providers/
│       ├── AppServiceProvider.php 🔧 MODIFIED ........... Registers StudentAnswerDetailObserver
│       └── RouteServiceProvider.php
│
├── 📁 bootstrap/
│   ├── app.php 🔧 MODIFIED ................................ Application configuration
│   │                                               (added: middleware alias 'role')
│   └── providers.php
│
├── 📁 config/ ............................................ Configuration files
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   ├── cache.php
│   └── ...
│
├── 📁 database/
│   ├── 📁 migrations/ .................................... Database schema
│   │   ├── 0001_01_01_000000_create_users_table.php .... User table (base auth)
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2025_01_04_000001_create_lessons_table.php
│   │   ├── 2025_01_04_000002_create_bai_tests_table.php
│   │   ├── 2025_01_04_000003_create_cau_hois_table.php
│   │   ├── 2025_01_04_000004_create_dap_ans_table.php
│   │   ├── 2025_01_04_000005_create_student_test_results_table.php
│   │   ├── 2025_01_04_000006_create_student_answer_details_table.php
│   │   ├── 2025_01_04_000007_create_course_enrollments_table.php
│   │   ├── 2025_01_04_000008_create_lesson_progresses_table.php
│   │   │
│   │   └── ❌ DELETED (Hospital References):
│   │       ├── 2025_06_23_045509_create_chi_tiet_dat_lichs_table.php
│   │       ├── 2025_06_23_050044_create_chuc_nangs_table.php
│   │       ├── 2025_06_23_050532_create_phong_khams_table.php
│   │       ├── 2025_06_23_053346_create_chuyen_khoas_table.php
│   │       ├── 2025_06_23_053700_create_chuyen_nganhs_table.php
│   │       ├── 2025_06_23_122902_create_chuc_vus_table.php
│   │       ├── 2025_06_23_161721_create_lich_lam_viecs_table.php
│   │       ├── 2025_06_23_162120_create_phieu_dat_liches_table.php
│   │       ├── 2025_06_23_162410_create_phan_quyens_table.php
│   │       ├── 2025_06_23_162617_create_admins_table.php
│   │       └── 2025_06_30_084930_create_slides_table.php
│   │
│   ├── 📁 factories/
│   │   └── UserFactory.php
│   │
│   └── 📁 seeders/ ...................................... (Optional: for initial data)
│       └── AdminSeeder.php
│
├── 📁 routes/
│   ├── api.php 🔧 MODIFIED ................................ All API routes
│   │                                            (added: admin routes, normalized auth)
│   ├── web.php
│   └── console.php
│
├── 📁 public/
│   └── index.php ........................................ Entry point
│
├── 📁 resources/
│   ├── 📁 css/
│   └── 📁 js/
│
├── 📁 storage/ .......................................... Logs, cache
│   ├── 📁 app/
│   ├── 📁 framework/
│   └── 📁 logs/
│
├── 📁 tests/
│   ├── Pest.php
│   ├── TestCase.php
│   ├── 📁 Feature/ ...... API endpoint tests
│   └── 📁 Unit/ ........ Unit tests
│
├── 📁 vendor/ ........................................... Dependencies (Composer)
│
├── 📄 artisan ............................................. Artisan CLI entry point
├── 📄 composer.json ..................................... PHP dependencies
├── 📄 composer.lock ..................................... Locked dependencies
├── 📄 package.json ..................................... Node dependencies (Vue)
├── 📄 package-lock.json ................................. Locked Node dependencies
├── 📄 vite.config.js ................................... Vite bundler config
├── 📄 phpunit.xml ....................................... PHPUnit test config
│
├── 📄 .env .............................................. Environment configuration
├── 📄 .env.example ..................................... Environment template
├── 📄 .gitignore ....................................... Git ignore rules
│
├── 📄 README.md ......................................... Project README
│
└── 📄 🆕 DOCUMENTATION (Generated)
    ├── REFACTOR_SUMMARY.md .......................... High-level summary (THIS YOU ARE READING)
    ├── REFACTOR_CHECKLIST.md ...................... Detailed requirement verification
    └── API_TESTING_GUIDE.md ....................... API endpoint testing with curl
```

---

## Key Structural Changes

### ✨ Added

```
app/Models/
├── GiaoVien.php ..................... Teacher model (User filtered by role)
└── HocSinh.php ...................... Student model (User filtered by role)

app/Http/Controllers/
└── AdminController.php ............. Teacher management (create teachers)

app/Http/Requests/
└── StoreTeacherRequest.php ........ Teacher creation validation
```

### 🔧 Modified

```
app/Http/Controllers/
└── BaiTestController.php ........... Simplified submitTest() (scoring moved to Observer)

app/Observers/
└── StudentAnswerDetailObserver.php . Added observer for auto-scoring

app/Providers/
└── AppServiceProvider.php .......... Register StudentAnswerDetailObserver

bootstrap/
└── app.php ......................... Added middleware alias 'role'

routes/
└── api.php ......................... Added admin routes, normalized auth endpoints
```

### ❌ Deleted

```
app/Http/Controllers/
├── BacSiController.php ........... Doctor management (hospital)
└── PhieuDatLichController.php .... Appointment management (hospital)

database/migrations/ (11 files)
└── All hospital-related tables ... Removed completely
```

---

## Database Schema Relationships

```
┌─────────────────────────────────────────────────────────────────┐
│                        users (auth)                              │
│  ├─ id (PK)                                                      │
│  ├─ name, email, password, role (admin/giao_vien/hoc_sinh)      │
│  └─ is_active, timestamps                                       │
└─────────────────────────────────────────────────────────────────┘
            │
    ┌───────┴───────┐
    │               │
    v               v

┌──────────────┐  ┌─────────────────────┐
│  lessons     │  │  student_test...    │
│  ├─ id       │  │  ├─ id              │
│  ├─ id_giao_vien (FK → users)         │  ├─ id_hoc_sinh (FK → users)
│  ├─ tieu_de  │  │  ├─ id_bai_test     │
│  └─ ...      │  └─ diem_tong, ...     │
└──────┬───────┘  └──────────┬──────────┘
       │                     │
       v                     v

┌──────────────┐           ┌────────────────────┐
│  bai_tests   │           │ student_answer...  │
│  ├─ id       │           │ ├─ id              │
│  ├─ id_lesson│           │ ├─ id_student...   │
│  └─ ...      │           │ ├─ id_cau_hoi      │
└──────┬───────┘           │ ├─ id_dap_an       │
       │                   │ ├─ diem_cau_hoi    │
       v                   │ └─ la_dung         │

┌──────────────┐           └────────────────────┘
│  cau_hois    │
│  ├─ id       │
│  ├─ id_bai_test (FK)     (points back to cau_hois)
│  └─ ...      │
└──────┬───────┘
       │
       v
┌──────────────┐
│  dap_ans     │
│  ├─ id       │
│  ├─ id_cau_hoi (FK)
│  ├─ noi_dung │
│  └─ la_dap_an_dung
└──────────────┘
```

---

## File Statistics

| Category                      | Count |
| ----------------------------- | ----- |
| **New Files**                 | 4     |
| **Modified Files**            | 3     |
| **Deleted Files**             | 13    |
| **Education Models**          | 11    |
| **API Endpoints**             | 21+   |
| **FormRequest Classes**       | 5     |
| **Controllers**               | 4     |
| **Database Tables (active)**  | 11    |
| **Database Tables (removed)** | 11    |

---

## Navigation Guide

### For API Development

→ See `routes/api.php` for endpoint definitions
→ See `app/Http/Controllers/` for business logic
→ See `app/Http/Requests/` for validation

### For Database Design

→ See `database/migrations/` for schema
→ See `app/Models/` for relationships

### For Authentication

→ See `app/Http/Controllers/AuthController.php`
→ See `app/Http/Middleware/CheckRole.php`

### For Scoring Logic

→ See `app/Observers/StudentAnswerDetailObserver.php`
→ See `app/Models/StudentAnswerDetail.php`

### For Testing

→ See `API_TESTING_GUIDE.md` for curl examples
→ See `tests/` directory for unit/feature tests

---

## Quick Reference: File Locations

| Need                   | File                                     |
| ---------------------- | ---------------------------------------- |
| Add new endpoint       | `routes/api.php`                         |
| Add validation         | `app/Http/Requests/NewRequest.php`       |
| Add business logic     | `app/Http/Controllers/...Controller.php` |
| Add model relationship | `app/Models/...Model.php`                |
| Add database table     | `database/migrations/...`                |
| Change middleware      | `app/Http/Middleware/CheckRole.php`      |
| Add background task    | `app/Jobs/`                              |

---

## Environment Setup

```bash
# Copy template
cp .env.example .env

# Key settings:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=english_learning
DB_USERNAME=root
DB_PASSWORD=

APP_DEBUG=false  # Set true for development
SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000
```

---

_Generated: January 13, 2026_
_Structure: Laravel 10 with Sanctum + Vue 3_
