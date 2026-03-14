# ⚡ Quick Reference Guide

## 🚀 Start Here

### 1. First Run

```bash
cd c:\xampp\htdocs\BE_TiengAnh
cp .env.example .env
# Edit .env with your database credentials
php artisan migrate
php artisan serve
```

### 2. First User (Admin)

```bash
php artisan tinker
```

```php
User::create([
    'name' => 'Admin',
    'email' => 'admin@test.com',
    'password' => Hash::make('admin123'),
    'role' => 'admin',
    'is_active' => true,
]);
exit;
```

### 3. Test Authentication

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@test.com","password":"admin123"}'
```

---

## 📚 Key Concepts

### Three-Role System

| Role          | Can Do                      | Cannot Do               |
| ------------- | --------------------------- | ----------------------- |
| **admin**     | Login, create teachers      | Self-register, teach    |
| **giao_vien** | Login, create lessons/tests | Register, create admins |
| **hoc_sinh**  | Register, login, take tests | Teach, create admins    |

### Scoring Logic

-   **Objective Q**: Auto-scored immediately
-   **Essay Q**: Null until teacher grades
-   **Total**: Auto-calculated on submit

### Auth Method

-   **POST /api/login** → Get token
-   **Use token** → `Authorization: Bearer {token}` header
-   **POST /api/logout** → Delete token

---

## 🔍 Common Tasks

### Create a Teacher (Admin Only)

```bash
curl -X POST http://localhost:8000/api/admin/teachers \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Teacher Name",
    "email": "teacher@example.com",
    "password": "TeacherPass123"
  }'
```

### Create a Lesson (Teacher)

```bash
curl -X POST http://localhost:8000/api/lessons \
  -H "Authorization: Bearer TEACHER_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "tieu_de": "Unit 1",
    "mo_ta": "Basic English",
    "noi_dung": "Content...",
    "trang_thai": 2
  }'
```

### Submit Test (Student)

```bash
curl -X POST http://localhost:8000/api/bai-tests/1/submit \
  -H "Authorization: Bearer STUDENT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "answers": [
      {"id_cau_hoi": 1, "id_dap_an": 2}
    ]
  }'
```

---

## 📁 File Quick Paths

| What        | File                                            |
| ----------- | ----------------------------------------------- |
| Models      | `app/Models/*.php`                              |
| Controllers | `app/Http/Controllers/*.php`                    |
| Routes      | `routes/api.php`                                |
| Validation  | `app/Http/Requests/*.php`                       |
| Middleware  | `app/Http/Middleware/CheckRole.php`             |
| Scoring     | `app/Observers/StudentAnswerDetailObserver.php` |
| Database    | `database/migrations/*.php`                     |

---

## 🐛 Debugging Tips

### Check Routes

```bash
php artisan route:list | grep api
```

### Check Middleware

```bash
php artisan route:list | grep role
```

### Test Connection

```bash
php artisan tinker
User::count();  // Should return > 0
```

### View SQL Queries

```bash
# In .env:
APP_DEBUG=true
LOG_CHANNEL=single

# In controller:
DB::listen(function ($query) {
    Log::info($query->sql);
});
```

---

## ✅ Pre-Deploy Checklist

-   [ ] Database migrations run (`php artisan migrate`)
-   [ ] `.env` configured with database
-   [ ] At least one admin user created
-   [ ] `php artisan optimize` run
-   [ ] CORS configured (if frontend on different domain)
-   [ ] Test login works
-   [ ] Test role middleware works
-   [ ] Test scoring observer works

---

## 🆘 Common Issues

| Problem              | Solution                                              |
| -------------------- | ----------------------------------------------------- |
| 401 Unauthorized     | Check `Authorization: Bearer {token}` header          |
| 403 Forbidden        | Check user role matches route requirement             |
| 500 Error            | Check `.env` DB config, check logs in `storage/logs/` |
| Observer not scoring | Verify registered in `AppServiceProvider.php`         |
| Lesson not public    | Set `trang_thai = 2` (1 = draft, 2 = published)       |

---

## 📊 Database Commands

```bash
# Create migration
php artisan make:migration create_table_name

# Run migrations
php artisan migrate

# Rollback
php artisan migrate:rollback

# Fresh (drop all, re-run)
php artisan migrate:fresh

# Seed data
php artisan db:seed

# Connect to tinker
php artisan tinker
```

---

## 🧪 Testing Commands

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test tests/Feature/AuthTest.php

# Run with coverage
php artisan test --coverage
```

---

## 📝 Model Relationships Cheat Sheet

```php
// User → Lessons (as teacher)
$user->lessons()

// User → Tests (as teacher)
$user->baiTests()

// User → Test Results (as student)
$user->studentTestResults()

// Lesson → Tests
$lesson->baiTests()

// Test → Questions
$baiTest->cauHois()

// Question → Answers
$cauHoi->dapAns()

// Question → Student Answers
$cauHoi->studentAnswerDetails()

// Test Result → Detail Answers
$result->studentAnswerDetails()
```

---

## 🔑 Environment Variables

```
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=english_learning
DB_USERNAME=root
DB_PASSWORD=

# App
APP_NAME="English Learning"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost

# Auth
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
SESSION_DOMAIN=localhost
```

---

## 📖 Documentation Files

| File                      | Purpose                           |
| ------------------------- | --------------------------------- |
| **REFACTOR_SUMMARY.md**   | High-level overview of changes    |
| **REFACTOR_CHECKLIST.md** | Detailed requirement verification |
| **API_TESTING_GUIDE.md**  | API endpoint testing guide        |
| **PROJECT_STRUCTURE.md**  | File/folder organization          |
| **This file**             | Quick reference                   |

---

## 🎯 Next Steps

1. **Development**

    - [ ] Read `REFACTOR_SUMMARY.md`
    - [ ] Review `routes/api.php`
    - [ ] Test endpoints in `API_TESTING_GUIDE.md`

2. **Deployment**

    - [ ] Configure `.env`
    - [ ] Run migrations
    - [ ] Create admin user
    - [ ] Test each role

3. **Frontend Integration**
    - [ ] Install Axios
    - [ ] Set auth header: `Authorization: Bearer {token}`
    - [ ] Handle 401/403 responses
    - [ ] Implement logout

---

## 💡 Pro Tips

1. **Use Postman** for API testing (more visual than curl)
2. **Enable query logging** during development to spot N+1 problems
3. **Use `php artisan tinker`** to test models/relationships
4. **Check `storage/logs/`** when something breaks
5. **Use FormRequests** for all input validation
6. **Use middleware** for role checks, not in controllers

---

## 📞 Support Resources

-   Laravel docs: https://laravel.com/docs/10
-   Sanctum docs: https://laravel.com/docs/10/sanctum
-   Eloquent docs: https://laravel.com/docs/10/eloquent

---

_Last Updated: January 13, 2026_
_Version: 1.0 - Refactor Complete_
