# 🚀 QUICK START GUIDE - Exam Management System

## ⏱️ 5-Minute Setup

### Step 1: Backend (Laravel) - 2 minutes

```bash
cd BE

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
# Edit .env with your database info, then:
php artisan migrate

# Start backend
php artisan serve
# Server runs at: http://localhost:8000
```

### Step 2: Frontend (Vue 3) - 2 minutes

```bash
cd FE

# Install dependencies (includes new chart.js)
npm install

# Start development server
npm run dev
# App runs at: http://localhost:5173
```

### Step 3: Access the App - 1 minute

1. Open browser to `http://localhost:5173`
2. Login with credentials (create/use existing account)
3. Start testing!

---

## 👤 Demo Users

### Teacher Account

```
Email: teacher@example.com
Password: password
Role: giao_vien (Teacher)
```

### Student Account

```
Email: student@example.com
Password: password
Role: hoc_sinh (Student)
```

---

## 🎯 First Test Creation (Teacher)

1. **After login**, click "📋 Danh Sách Bài Test"
2. Click blue button "➕ Tạo Bài Test Mới"
3. **Fill in:**
   - Tên: "English Grammar Quiz"
   - Thời Gian: 30 phút
   - Điểm Tối Đa: 100
   - Điểm Đạt: 60
4. **Check boxes:**
   - ✓ Trộn Lẫn Câu Hỏi
   - ✓ Trộn Lẫn Đáp Án
   - ✓ Hiển Thị Kết Quả Ngay
5. Click "Thêm Câu Hỏi" → Add question:
   - Type: Multiple Choice
   - Content: "What is the past tense of 'go'?"
   - Answers: went (✓ correct), goes, going
6. Click "Lưu Câu Hỏi"
7. Add 2-3 more questions
8. Click "Tạo Bài Test" button

✅ Test created! Set status to "📢 Công Bố" (Published)

---

## 🎓 First Test Taking (Student)

1. **After login as student**, click "📋 Danh Sách Bài Test"
2. Find the test you created
3. Click "Xem" (View)
4. Click "🚀 Start Test" button
5. **Timer starts** ⏱️
6. Answer the questions:
   - Multiple Choice: Select radio button
   - Fill Blank: Type in text box
   - Matching: Select dropdowns
7. Click "Nộp Bài Test" (Submit Test)
8. **See results** immediately! 🎉
9. Click "📊 View Results" for full breakdown

---

## 📊 View Analytics (Teacher)

1. Go to test list
2. Click analytics icon 📊 on any test
3. See:
   - 4 KPI cards at top
   - Score distribution chart
   - Pass/Fail pie chart
   - Question breakdown table
   - Student attempts list
4. Search students by name
5. Click on student attempt to see details
6. Click "Xuất Excel CSV" to download results

---

## 📱 Component Guide

### For Students

| Screen       | URL                 | Purpose                |
| ------------ | ------------------- | ---------------------- |
| Test List    | `/tests`            | Browse available tests |
| Take Quiz    | `/tests/:id/take`   | Answer questions       |
| View Results | `/tests/:id/result` | See score & answers    |

### For Teachers

| Screen       | URL                    | Purpose             |
| ------------ | ---------------------- | ------------------- |
| My Tests     | `/tests`               | Create/manage tests |
| Build Test   | `/tests/create`        | Add questions       |
| View Results | `/tests/:id/analytics` | Student analytics   |

---

## 🧠 Understanding Question Types

### 1. **Multiple Choice** 📋

- Student selects ONE correct answer
- Auto-graded
- Full points or 0

### 2. **True/False** ✓✗

- Student chooses ✓ (True) or ✗ (False)
- Auto-graded
- Full points or 0

### 3. **Essay** 📝

- Student writes paragraph answer
- Teacher grades manually
- Custom points awarded

### 4. **Matching** 🔗

- Student pairs LEFT items with RIGHT items
- Auto-graded with partial credit
- Points per correct pair

### 5. **Fill Blank** \_\_\_

- Student types short answer
- Case-insensitive matching
- Auto-graded
- Can accept multiple correct answers

### 6. **Image Choice** 🖼️

- Student clicks on correct image
- Auto-graded
- Full points or 0

---

## ⚙️ Test Settings Explained

| Setting               | Effect                                            |
| --------------------- | ------------------------------------------------- |
| Trộn Lẫn Câu Hỏi      | Questions appear in random order each time        |
| Trộn Lẫn Đáp Án       | Answer options randomized (except essay/matching) |
| Hiển Thị Kết Quả Ngay | Student sees score immediately after submit       |
| Hiển Thị Đáp Án       | Student sees which answers were correct           |
| Số Lần Làm Tối Đa     | How many times student can retake (1-10)          |
| Thời Gian Tối Đa      | Minutes allowed per attempt                       |
| Điểm Tối Đa           | Total points for test                             |
| Điểm Đạt              | Minimum score to pass                             |

---

## 🔑 Key Features

✅ **Shuffle per attempt** - Each student gets different order  
✅ **Auto-submit on timeout** - No cheating with time limits  
✅ **6 Question types** - Flexible test design  
✅ **Instant grading** - Most types auto-graded, essays pending  
✅ **Retake limits** - Control attempt frequency  
✅ **Analytics** - Charts, predictions, difficulty analysis  
✅ **Mobile-ready** - Works on phones/tablets

---

## 🐛 If Something Goes Wrong

### Backend not starting?

```bash
cd BE
php artisan serve
# Should say "Laravel development server started"
# If port 8000 in use, try: php artisan serve --port=8001
```

### Frontend not loading?

```bash
cd FE
npm run dev
# Should say "VITE v4.x.x ready in xxx ms"
# Open http://localhost:5173
```

### API calls failing?

```
1. Check backend is running
2. Open DevTools (F12)
3. Go to Network tab
4. Check API responses
5. Review backend logs: BE/storage/logs/
```

### Clear everything and restart?

```bash
# Backend
cd BE
php artisan migrate:fresh
php artisan serve

# Frontend (in new terminal)
cd FE
rm -rf node_modules
npm install
npm run dev
```

---

## 📚 Next Steps

1. **Create test** → Practice test creation flow
2. **Take test** → Student experience
3. **View results** → Check auto-grading works
4. **See analytics** → Teacher dashboard
5. **Retake test** → Verify retake limits
6. **Export results** → CSV download

---

## 📖 Full Documentation

For detailed information, see:

- 📄 Root: `README.md` (Setup & overview)
- 📄 Backend: `BE/EXAM_MANAGEMENT_COMPLETE.md` (API reference)
- 📄 Frontend: `FE/FRONTEND_IMPLEMENTATION_GUIDE.md` (Component guide)
- 📄 Summary: `PROJECT_COMPLETION_SUMMARY.md` (Full details)

---

## 🎉 You're Ready!

The system is fully set up. Start with:

1. Create a test (teacher)
2. Take the test (student)
3. View results & analytics

Questions? Check documentation files in BE/ and FE/ directories.

Happy testing! 🚀

---

**Version:** 1.0.0  
**Status:** Ready to Use ✅  
**Est. Setup Time:** 5 minutes ⏱️
