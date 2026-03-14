# 📋 Frontend - Exam Management Component Implementation

## Overview

Complete Vue 3 + Pinia frontend implementation for exam management system with 6 new components and route configuration.

---

## Architecture

### Component Hierarchy

```
App.vue
├── Router (vue-router)
│   ├── TestList.vue (List & Manage Tests)
│   ├── TestBuilder.vue (Create/Edit Tests)
│   │   └── QuestionEditor.vue (Modal - Question Editor)
│   ├── TestTaker.vue (Take Test Interface)
│   ├── ResultPage.vue (View Results)
│   └── AnalyticsDashboard.vue (Teacher Analytics)
│
├── Pinia Stores
│   ├── auth.js (User Authentication)
│   └── test.js (Test State Management) ← NEW
│
└── API Services
    ├── axiosClient.js (HTTP Client)
    └── testApi.js (Test API Methods) ← UPDATED
```

### Data Flow

```
Vue Component
    ↓
useTestStore() (Pinia)
    ↓
testApi.* (API Methods)
    ↓
http.get/post/put/delete (Axios)
    ↓
Backend API (/api/bai-tests/*)
    ↓
Laravel Controller
    ↓
Database
```

---

## Component Details

### 1. TestList.vue

**Purpose:** Display and manage tests  
**Location:** `src/views/TestList.vue`  
**Users:** Teachers (browse own), Students (browse available)

**Key Features:**

- Search bar with debounced input
- Filter by status (Draft/Published)
- Sort options (Newest, Name A-Z, Recently Updated)
- Pagination with page numbers
- Hover card effects
- Edit/Delete/View/Analytics buttons (for teachers)
- Create new test button

**Props:** None (uses Pinia store)  
**Emits:** None (uses router navigation)

**Key Methods:**

- `onSearchChange()` - Debounced search (500ms)
- `onFilterChange()` - Apply filters to state
- `fetchData()` - Load tests based on user role
- `goToPage(page)` - Pagination handler
- `onDeleteTest(id)` - Delete with confirmation

**State (from store):**

```javascript
tests[] // Current page tests
loading // Loading state
pagination // Page info: currentPage, lastPage, total
filters // search, status, sortBy, sortOrder, currentPage
```

**Computed:**

- `isTeacher` - Current user is teacher
- `pageNumbers` - Array of page numbers to display
- `truncate()` - Truncate long strings

---

### 2. TestBuilder.vue

**Purpose:** Create and edit tests with question management  
**Location:** `src/views/TestBuilder.vue`  
**Users:** Teachers only

**Key Features:**

- Test settings form (name, time, points, retake limit)
- Shuffle options (questions, answers)
- Result visibility settings
- Status selector (Draft/Published)
- Question list with add/edit/delete
- QuestionEditor modal for form
- Right sidebar showing test summary

**Props:** None  
**Emits:** None (uses router)

**Key Sections:**

1. **Settings Panel** - Test configuration
2. **Summary Panel** - Live preview of settings
3. **Questions Panel** - Manage test questions

**Key Methods:**

- `openQuestionEditor(question?)` - Open modal (new or edit)
- `onQuestionSave(question)` - Save question to list
- `deleteQuestion(index)` - Remove question
- `onSubmit()` - Create/update test
- `loadTest()` - Load existing test if editing

**Form Schema:**

```javascript
{
  ten_bai_test: "",
  mo_ta: "",
  thoi_gian_toi_da: 60,
  diem_tong_max: 100,
  so_lan_lam_toi_da: 3,
  diem_dat: 60,
  tro_luc_cau_hoi: true,
  tro_luc_dap_an: true,
  hien_thi_ket_qua_ngay: true,
  hien_thi_dap_an_dung: true,
  trang_thai: 1,
}
```

---

### 3. QuestionEditor.vue (Modal Component)

**Purpose:** Create/edit questions with support for 6 types  
**Location:** `src/components/QuestionEditor.vue`  
**Users:** Teachers (via TestBuilder parent)

**Key Features:**

- 6 question type support:
  - 📋 Multiple Choice (Radio buttons)
  - ✓✗ True/False
  - 📝 Essay (Manual grading)
  - 🔗 Matching (Pair left/right items)
  - \_\_\_ Fill Blank (Multiple correct answers)
  - 🖼️ Image Choice (Select from images)
- Image URL support
- Answer management (add/remove/mark correct)
- Detailed description and notes fields
- Points/scoring field

**Props:**

```javascript
{
  question: Object (default: null),
  loaiCauHoi: String (default: "multiple_choice")
}
```

**Emits:**

- `save(question)` - Form submitted
- `close()` - Modal closed

**Key Methods:**

- `initializeAnswers()` - Set default answer structure based on type
- `addAnswer()` - Add blank answer
- `removeAnswer(index)` - Remove answer
- `onSave()` - Validate and emit save
- Dynamic UI based on `form.loai_cau_hoi`

**Form Schema:**

```javascript
{
  loai_cau_hoi: "multiple_choice",
  noi_dung: "",
  hinh_anh_url: "",
  mo_ta_chi_tiet: "",
  ghi_chu: "",
  diem_toi_da: 1,
  answers: [],
  dap_an_mau: "", // For essay
}
```

**Answer Schemas by Type:**

- Multiple Choice/True-False: `{ noi_dung, hinh_anh_url, la_dap_an_dung }`
- Matching: `{ trai, phai }` (pair)
- Fill Blank: `{ noi_dung }` (can have multiple)
- Essay: No answers array

---

### 4. TestTaker.vue

**Purpose:** Interface for students to take tests  
**Location:** `src/views/TestTaker.vue`  
**Users:** Students only

**Key Features:**

- Countdown timer (RED when < 1min, YELLOW < 5min)
- Auto-submit on timeout
- Shuffled questions (server-side per attempt)
- Question display by type with dynamic form fields
- Radio buttons for multiple choice
- Checkboxes for true/false
- Image selection with border highlighting
- Matching dropdown pairs
- Text input for fill blank
- Textarea for essays
- Submit button with confirmation

**Props:** None (route param: `id`)  
**Emits:** None (uses router)

**Key Methods:**

- `initializeAnswers()` - Create answer array structure
- `startTimer()` - Begin 1s countdown
- `onSubmitTest()` - Validate and submit
- `loadTest()` - Fetch test and questions
- `getQuestion()` - Get shuffled questions

**State:**

```javascript
testId,
currentTest: {},
questions: [],
studentAnswers: [],
submitted: false,
timeRemaining: 0,
timerInterval,
```

**Computed:**

- `shuffledQuestions` - Server shuffled if enabled
- `formattedTime` - MM:SS format
- `timerClass` - CSS class based on time remaining

**Display Logic by Type:**

- **multiple_choice**: Radio button group
- **true_false**: Two radio buttons (✓ Đúng / ✗ Sai)
- **image_choice**: Grid of images with radio overlay
- **matching**: Two columns with left items and right dropdown select
- **fill_blank**: Single text input
- **essay**: Textarea

---

### 5. ResultPage.vue

**Purpose:** Display test results and scores  
**Location:** `src/views/ResultPage.vue`  
**Users:** Students (view own), Teachers (view student attempts)

**Key Features:**

- Large score display with color indicator (Green/Yellow/Red)
- Progress bar showing percentage
- Pass/Fail indicator
- Score details card (time, attempts, date)
- Per-question review section
- Show/hide correct answers based on test settings
- Student answer vs. correct answer comparison
- Retake button (if attempts remaining)
- Print button
- Back to list button

**Props:** None (route params: `id`, optional `attemptId`)  
**Emits:** None (uses router)

**Key Methods:**

- `formatDate(date)` - Format to locale string
- `isAnswerCorrect(question, index)` - Check correctness
- `getAnswerDisplay(question, index)` - Format student answer
- `getCorrectAnswerDisplay(question)` - Format correct answer
- `shouldShowAnswers(question, index)` - Check visibility
- `getQuestionScore(index)` - Get points for question
- `onRetake()` - Navigate to retake test
- `onPrint()` - Trigger browser print
- `loadResult()` - Fetch result data

**State:**

```javascript
testId,
result: {},
questions: [],
studentAnswers: [],
error: "",
```

**Computed:**

- `scorePercentage` - Calculated from score/max
- `scoreColor` - RGB color based on percentage
- `isPassed` - Boolean check against passing score
- `statusText` - Formatted status string
- `canRetake` - Check if retakes available

**Display by Question Type:**

- **Multiple Choice**: Show selected option vs. correct
- **True/False**: Show ✓ or ✗ vs. correct
- **Matching**: Show paired items vs. correct pairs
- **Fill Blank**: Show typed text vs. correct answer(s)
- **Essay**: Show student response + manual grade status

---

### 6. AnalyticsDashboard.vue

**Purpose:** Teacher analytics and insights  
**Location:** `src/views/AnalyticsDashboard.vue`  
**Users:** Teachers only

**Key Features:**

- Test information card
- 4 KPI cards: Students, Avg Score, Pass Rate, Avg Time
- Bar chart for score distribution (0-20, 20-40, etc.)
- Pie chart for pass/fail ratio
- Question-by-question analytics table:
  - Difficulty level (Easy/Medium/Hard)
  - % of students who got it correct
  - Average score per question
- Student attempts table with:
  - Search/filter by name/email
  - Score, percentage, time
  - Status badge
  - View button per attempt
- CSV export button

**Props:** None (route param: `id`)  
**Emits:** None

**Key Methods:**

- `initCharts()` - Initialize Chart.js instances
- `getScoreDistribution()` - Calculate score ranges
- `formatDate(date)` - Format timestamps
- `getStatusText(status)` - Localize status
- `getQuestionCorrectRate(question)` - % correct
- `getQuestionAverageScore(question)` - Avg points
- `getDifficulty(question)` - Easy/Medium/Hard
- `onExportCSV()` - Generate and download CSV
- `loadAnalytics()` - Fetch analytics data

**State:**

```javascript
testId,
test: {},
questions: [],
attempts: [],
chartInstance: null,
pieChartInstance: null,
searchStudent: "",
```

**Computed:**

- `totalStudents` - Unique student count (Set)
- `averageScore` - Mean score across attempts
- `passedCount` / `failedCount` - Pass rates
- `passRate` - Percentage
- `averageTime` - Avg time in minutes
- `filteredAttempts` - Search-filtered results

**Chart Libraries:**

- `chart.js/auto` for visualizations

---

## Pinia Store (test.js)

**File:** `src/stores/test.js`

### State

```javascript
{
  tests: [],
  currentTest: {},
  currentAttempt: {},
  studentAnswers: [],
  testResult: {},
  analytics: {},
  loading: false,
  error: "",

  pagination: {
    total: 0,
    per_page: 10,
    current_page: 1,
    last_page: 1,
  },

  filters: {
    search: "",
    status: null,
    sortBy: "created_at",
    sortOrder: "desc",
    currentPage: 1,
  },
}
```

### Key Actions (Async)

- `fetchTestsByLesson(lessonId, params)` - Get available tests
- `fetchMyTests()` - Get user's tests (pagination)
- `fetchTestDetail(testId)` - Get full test + questions
- `createTest(data)` - Create new test
- `updateTest(testId, data)` - Update test
- `deleteTest(testId)` - Delete test
- `startTest(testId)` - Begin test attempt
- `submitTest(testId, answers)` - Submit answers
- `fetchResult(testId)` - Get result data
- `fetchQuestions(testId)` - Get questions
- `createQuestion(testId, data)` - Add question
- `updateQuestion(testId, questionId, data)` - Edit question
- `deleteQuestion(testId, questionId)` - Remove question
- `createAnswer(testId, questionId, data)` - Add answer
- `updateAnswer(testId, questionId, answerId, data)` - Edit answer
- `deleteAnswer(testId, questionId, answerId)` - Remove answer
- `recordAnswer(questionIndex, answer)` - Track student response
- `fetchAnalytics(testId)` - Get analytics data
- `updateFilters(filters)` - Update filter state
- `resetFilters()` - Clear all filters
- `resetCurrentTest()` - Clear test state
- `resetResult()` - Clear result state

### Computed Properties

- `filteredTests` - Filtered + sorted test list
- `totalTime` - Total test duration in seconds
- `hasMoreAttempts` - Boolean check for retakes

---

## API Integration

### Test List

```javascript
// GET /api/bai-tests?search=...&status=...&sort_by=...&sort_order=...&page=...
const tests = await testStore.fetchTestsByLesson(lessonId, {
  search: "keyword",
  status: 2, // 1=Draft, 2=Published
  sort_by: "created_at",
  sort_order: "desc",
  page: 1,
});
```

### Test Details

```javascript
// GET /api/bai-tests/:id
const test = await testStore.fetchTestDetail(testId);
// Returns: { id, ten_bai_test, mo_ta, questions: [...], diem_tong_max, ... }
```

### Submit Test

```javascript
// POST /api/bai-tests/:id/submit
await testStore.submitTest(testId, {
  responses: [
    { question_id: 1, answer: "value" },
    ...
  ],
  time_used: 1800, // seconds
})
```

### Analytics

```javascript
// GET /api/bai-tests/:id/analytics
const analytics = await testStore.fetchAnalytics(testId);
// Returns: { test_analytic: {...}, question_analytics: [...], attempts: [...] }
```

---

## Router Configuration

**File:** `src/router/index.js`

### Routes Added

```javascript
{
  path: "/tests",
  name: "TestList",
  component: () => import("@/views/TestList.vue"),
  meta: { layout: "admin", requiresAuth: true },
},
{
  path: "/tests/create",
  name: "CreateTest",
  component: () => import("@/views/TestBuilder.vue"),
  meta: { layout: "admin", requiresAuth: true, roles: ["giao_vien", "admin"] },
},
{
  path: "/tests/:id/edit",
  name: "EditTest",
  component: () => import("@/views/TestBuilder.vue"),
  meta: { layout: "admin", requiresAuth: true, roles: ["giao_vien", "admin"] },
},
{
  path: "/tests/:id/take",
  name: "TakeTest",
  component: () => import("@/views/TestTaker.vue"),
  meta: { layout: "client", requiresAuth: true, roles: ["hoc_sinh"] },
},
{
  path: "/tests/:id/result",
  name: "TestResult",
  component: () => import("@/views/ResultPage.vue"),
  meta: { layout: "client", requiresAuth: true },
},
{
  path: "/tests/:id/analytics",
  name: "TestAnalytics",
  component: () => import("@/views/AnalyticsDashboard.vue"),
  meta: { layout: "admin", requiresAuth: true, roles: ["giao_vien", "admin"] },
},
```

---

## Usage Examples

### Teacher Creating a Test

```javascript
// Components flow: TestBuilder → QuestionEditor (modal) → Submit
1. Navigate to /tests/create (TestBuilder.vue)
2. Fill form (name, time, points, options)
3. Click "Thêm Câu Hỏi" (Add Question)
4. QuestionEditor modal opens
5. Select question type (e.g., Multiple Choice)
6. Add answer options, mark correct
7. Save question → goes back to TestBuilder
8. Click "Tạo Bài Test" (Create Test)
9. Store calls testApi.createTest() → Backend processes → Navigate to TestList
```

### Student Taking a Test

```javascript
// Components flow: TestList → TestTaker → ResultPage
1. Student sees test in TestList.vue
2. Click "Xem" (View) → Goes to /tests/:id/take
3. TestTaker.vue loads test + starts timer
4. Answer questions dynamically based on type
5. Click "Nộp Bài Test" (Submit)
6. Store calls testApi.submitTest() → Auto-grades
7. Redirects to /tests/:id/result
8. ResultPage shows score, breakdown, allows retake
```

### Teacher Viewing Analytics

```javascript
// Components flow: TestList → AnalyticsDashboard
1. Teacher clicks analytics icon on test card
2. Navigate to /tests/:id/analytics
3. AnalyticsDashboard.vue loads analytics data
4. Display KPIs, charts, question breakdown, student list
5. Search students by name/email
6. Click on student attempt to see details
7. Export CSV of all results
```

---

## Styling & UI Features

### Bootstrap 5 + Custom CSS

- **Cards:** Shadow, hover effects, borders
- **Forms:** Consistent styling, validation feedback
- **Tables:** Hover rows, alternating colors, responsive
- **Badges:** Color-coded status indicators
- **Progress Bars:** Score visualization
- **Modals:** Fade animation on open
- **Buttons:** Primary/Secondary/Danger variants
- **Images:** Thumbnails, responsive sizing

### Key CSS Classes

- `.hover-card` - Card lift effect on hover
- `.progress` - Score percentage bar
- `.badge` - Status indicators
- `.alert` - Success/danger/info alerts
- `.spinner-border` - Loading indicator
- `.table-hover` - Row highlight on hover

---

## Testing Checklist

### Frontend Development

- [ ] All 6 components render without errors
- [ ] Router navigation works (all 7 routes)
- [ ] Pinia store initializes with correct state
- [ ] API calls use correct endpoints
- [ ] Timer counts down correctly
- [ ] Questions shuffle when enabled
- [ ] All 6 question types render properly
- [ ] Search/filter/pagination works
- [ ] Charts render with sample data
- [ ] CSV export generates file

### Integration with Backend

- [ ] Test creation endpoint returns 200
- [ ] Question submission returns correct IDs
- [ ] Timer enforcement blocks late submissions
- [ ] Auto-grading returns correct scores
- [ ] Analytics endpoint returns all required data
- [ ] Shuffle implementation matches backend
- [ ] Retake limit logic prevents extra attempts
- [ ] Status transitions work (not_started → completed)

---

## Files Created/Modified

### Created Files (6 Components)

1. `src/views/TestList.vue` - Test listing/management
2. `src/views/TestBuilder.vue` - Test creation/editing
3. `src/components/QuestionEditor.vue` - Question modal
4. `src/views/TestTaker.vue` - Quiz interface
5. `src/views/ResultPage.vue` - Results display
6. `src/views/AnalyticsDashboard.vue` - Analytics

### Modified Files

1. `src/router/index.js` - Added 7 new routes
2. `src/stores/test.js` - Created with 30+ actions
3. `src/api/testApi.js` - Verified existing methods

### Dependencies (Already installed)

- vue-router 4.x
- pinia 3.x
- axios 1.x
- bootstrap 5.x
- chart.js (for AnalyticsDashboard)

---

## Next Steps / Future Enhancements

1. **Advanced Features**
   - [ ] Drag-drop question reordering
   - [ ] Bulk operations (delete multiple)
   - [ ] Scheduled test publishing
   - [ ] Custom grading scales
   - [ ] Email notifications

2. **UI Improvements**
   - [ ] Dark mode theme
   - [ ] Mobile responsive optimization
   - [ ] Keyboard shortcuts
   - [ ] Accessibility (WCAG compliance)

3. **Performance**
   - [ ] Lazy loading of large result lists
   - [ ] Virtual scrolling for long lists
   - [ ] Caching strategies

4. **Testing**
   - [ ] Unit tests (Jest)
   - [ ] E2E tests (Cypress)
   - [ ] Component snapshots

---

## Troubleshooting

### Common Issues

1. **Questions not showing in TestTaker**
   - Ensure test has `questions` in response
   - Check `shuffledQuestions` computed property
   - Verify form initialization

2. **Timer not updating**
   - Ensure `setInterval` started in `onMounted`
   - Check interval cleanup in `onUnmounted`
   - Verify time format calculation (MM:SS)

3. **Charts not rendering in Analytics**
   - Ensure Chart.js is properly imported
   - Check canvas element has correct ID
   - Add 100ms delay before chart creation

4. **Form validation failing**
   - Check required field validation
   - Ensure all array validations pass
   - Review error messages in console

---

Generated: 2024
Status: Complete ✅
