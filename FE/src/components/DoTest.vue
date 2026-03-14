<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { getDetail, startTest, submitTest as submitTestApi } from '@/api/testApi';

const route = useRoute();
const router = useRouter();

// --- State ---
const testId = computed(() => route.params.testId);
const testName = ref('');
const testDescription = ref('');
const timeLeft = ref(0);
const currentQuestionIndex = ref(0);
const questions = ref([]);
const loading = ref(true);
const error = ref('');
const submitting = ref(false);

// --- Timer Logic ---
let timerInterval = null;

const formattedTime = computed(() => {
  const hours = Math.floor(timeLeft.value / 3600);
  const minutes = Math.floor((timeLeft.value % 3600) / 60);
  const seconds = timeLeft.value % 60;
  const h = hours ? `${hours.toString().padStart(2, '0')}:` : '';
  const m = minutes.toString().padStart(2, '0');
  const s = seconds.toString().padStart(2, '0');
  return `${h}${m}:${s}`;
});

const startTimerCountdown = () => {
  timerInterval = setInterval(() => {
    if (timeLeft.value > 0) {
      timeLeft.value--;
    } else {
      clearInterval(timerInterval);
      handleSubmit();
    }
  }, 1000);
};

// --- Load test data from API ---
const loadTest = async () => {
  loading.value = true;
  error.value = '';
  try {
    const res = await getDetail(testId.value);
    const data = res.data;

    testName.value = data.testName || '';
    testDescription.value = data.description || '';
    timeLeft.value = (data.maxTime || 60) * 60; // convert minutes to seconds

    questions.value = (data.questions || []).map((q) => ({
      id: q.id,
      content: q.content,
      answers: q.answers || [],
      userAnswer: null, // will hold the selected answer id
    }));

    // Register the test attempt
    await startTest(testId.value);
    startTimerCountdown();
  } catch (err) {
    const msg = err.response?.data?.message || err.message;
    error.value = msg || 'Không thể tải bài test';
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadTest();
});

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval);
});

// --- Actions ---
const selectQuestion = (index) => {
  currentQuestionIndex.value = index;
};

const answerQuestion = (answerId) => {
  questions.value[currentQuestionIndex.value].userAnswer = answerId;
};

const handleSubmit = async () => {
  if (submitting.value) return;
  if (!confirm('Bạn có chắc chắn muốn nộp bài?')) return;

  submitting.value = true;
  clearInterval(timerInterval);

  try {
    const payload = questions.value.map((q) => ({
      id_cau_hoi: q.id,
      id_dap_an: q.userAnswer,
    }));
    const res = await submitTestApi(testId.value, payload);
    alert('Hoàn thành! Điểm của bạn: ' + (res.data?.diem_tong ?? 'Chưa chấm'));
    router.push('/');
  } catch (err) {
    alert('Lỗi khi nộp bài: ' + (err.response?.data?.message || err.message));
  } finally {
    submitting.value = false;
  }
};

const isCurrent = (index) => currentQuestionIndex.value === index;
const isAnswered = (question) => question.userAnswer !== null;
</script>

<template>
  <div class="test-container">
    <!-- Loading State -->
    <div v-if="loading" class="loading-screen">
      <div class="spinner"></div>
      <p>Đang tải bài test...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-screen">
      <h2>⚠️ Không thể tải bài test</h2>
      <p>{{ error }}</p>
      <button class="nav-btn primary" @click="router.push('/')">Về trang chủ</button>
    </div>

    <!-- Test Content -->
    <template v-else>
      <!-- Sticky Header -->
      <header class="test-header">
        <div class="header-content">
          <h1 class="test-title">{{ testName }}</h1>
          <button class="submit-btn" :disabled="submitting" @click="handleSubmit">
            {{ submitting ? 'Đang nộp...' : 'Nộp bài' }}
          </button>
        </div>
      </header>

      <!-- Main Content Area -->
      <div class="test-body">

        <!-- Left Sidebar (Question Grid) -->
        <aside class="sidebar">
          <div class="timer-display">
            <span class="timer-icon">⏱️</span>
            {{ formattedTime }}
          </div>

          <div class="questions-grid">
            <div v-for="(q, index) in questions" :key="q.id" class="grid-item" :class="{
              'active': isCurrent(index),
              'completed': isAnswered(q)
            }" @click="selectQuestion(index)">
              {{ index + 1 }}
            </div>
          </div>

          <div class="legend">
            <div class="legend-item"><span class="dot completed"></span> Đã trả lời</div>
            <div class="legend-item"><span class="dot active"></span> Đang xem</div>
            <div class="legend-item"><span class="dot"></span> Chưa trả lời</div>
          </div>
        </aside>

        <!-- Right Main Content -->
        <main class="main-content">
          <section v-if="questions.length" class="question-panel">
            <div class="question-card">
              <div class="question-number">Câu {{ currentQuestionIndex + 1 }} / {{ questions.length }}</div>
              <p class="question-text">{{ questions[currentQuestionIndex].content }}</p>

              <div class="options-list">
                <label v-for="ans in questions[currentQuestionIndex].answers" :key="ans.id" class="option-label"
                  :class="{ 'selected': questions[currentQuestionIndex].userAnswer === ans.id }">
                  <input type="radio" :name="`q-${questions[currentQuestionIndex].id}`" :value="ans.id"
                    :checked="questions[currentQuestionIndex].userAnswer === ans.id" @change="answerQuestion(ans.id)">
                  <span class="option-text">{{ ans.content }}</span>
                </label>
              </div>

              <div class="navigation-buttons">
                <button v-if="currentQuestionIndex > 0" @click="currentQuestionIndex--" class="nav-btn">Câu
                  trước</button>
                <span v-else></span>
                <button v-if="currentQuestionIndex < questions.length - 1" @click="currentQuestionIndex++"
                  class="nav-btn primary">Câu tiếp</button>
              </div>
            </div>
          </section>

          <div v-else class="question-panel">
            <p>Bài test không có câu hỏi nào.</p>
          </div>
        </main>
      </div>
    </template>
  </div>
</template>

<style scoped>
/* Variables */
:root {
  --primary-color: #fc74dd;
  --bg-color: #f8f9fa;
  --text-color: #333;
  --border-radius: 12px;
  --sidebar-width: 30%;
  --header-height: 70px;
}

/* Base Layout */
.test-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  background-color: #f8f9fa;
  font-family: 'Inter', sans-serif;
  color: #333;
}

.loading-screen,
.error-screen {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100vh;
  gap: 1rem;
}

.spinner {
  width: 48px;
  height: 48px;
  border: 4px solid #eee;
  border-top-color: #fc74dd;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Header */
.test-header {
  height: 70px;
  background: white;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  display: flex;
  align-items: center;
  justify-content: center;
  position: sticky;
  top: 0;
  z-index: 100;
  padding: 0 2rem;
}

.header-content {
  width: 100%;
  max-width: 1400px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.test-title {
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0;
}

.submit-btn {
  padding: 0.6rem 1.5rem;
  border: none;
  border-radius: 20px;
  color: white;
  font-weight: 600;
  cursor: pointer;
  /* Gradient with primary color */
  background: linear-gradient(135deg, #fc74dd 0%, #ff4d9e 100%);
  transition: transform 0.2s, box-shadow 0.2s;
  box-shadow: 0 4px 15px rgba(252, 116, 221, 0.4);
}

.submit-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(252, 116, 221, 0.6);
}

/* Body Layout */
.test-body {
  display: flex;
  flex: 1;
  overflow: hidden;
  /* Prevent body scroll, let panels scroll */
  max-width: 1400px;
  width: 100%;
  margin: 0 auto;
  padding: 1rem;
  gap: 1rem;
}

/* Sidebar */
.sidebar {
  width: 30%;
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
  padding: 1.5rem;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.timer-display {
  font-size: 1.5rem;
  font-weight: 700;
  text-align: center;
  padding: 1rem;
  background: #fdfdfd;
  border: 1px solid #eee;
  border-radius: 12px;
  color: #333;
}

.questions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(40px, 1fr));
  gap: 10px;
}

.grid-item {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  /* Slightly rounded squares */
  background: #f0f2f5;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.9rem;
  color: #666;
  transition: all 0.2s ease;
}

.grid-item:hover {
  background: #e2e6ea;
}

/* Grid States */
.grid-item.completed {
  background-color: #e0f7fa;
  /* Light Green/Teal tint */
  color: #00897b;
  border: 1px solid #00897b;
}

.grid-item.active {
  border: 2px solid #fc74dd;
  color: #fc74dd;
  background: white;
}

.legend {
  margin-top: auto;
  font-size: 0.85rem;
  color: #666;
  display: flex;
  justify-content: space-around;
  padding-top: 1rem;
  border-top: 1px solid #f0f0f0;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
}

.dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #f0f2f5;
  border: 1px solid #ddd;
}

.dot.completed {
  background: #e0f7fa;
  border-color: #00897b;
}

.dot.active {
  border: 2px solid #fc74dd;
  background: white;
}

/* Main Content */
.main-content {
  width: 70%;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  overflow-y: auto;
  padding-right: 5px;
  /* Space for scrollbar */
}

.passage-panel,
.question-panel {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
}

.passage-panel h2 {
  margin-top: 0;
  color: #333;
}

.passage-panel p {
  line-height: 1.6;
  color: #555;
  margin-bottom: 1rem;
}

.question-number {
  font-weight: 700;
  color: #888;
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.question-text {
  font-size: 1.1rem;
  font-weight: 500;
  margin-bottom: 1.5rem;
}

.options-list {
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
}

.option-label {
  display: flex;
  align-items: center;
  padding: 1rem;
  border: 1px solid #eee;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s;
}

.option-label:hover {
  background: #f9f9f9;
  border-color: #ddd;
}

.option-label.selected {
  background: #fff0fa;
  /* Very light pink tint */
  border-color: #fc74dd;
}

.option-label input {
  margin-right: 1rem;
  accent-color: #fc74dd;
}

.navigation-buttons {
  margin-top: 2rem;
  display: flex;
  justify-content: space-between;
}

.nav-btn {
  background: white;
  border: 1px solid #ddd;
  padding: 0.6rem 1.5rem;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
}

.nav-btn:hover {
  background: #f5f5f5;
}

.nav-btn.primary {
  background: #333;
  color: white;
  border: none;
}

.nav-btn.primary:hover {
  background: #555;
}

/* Responsive */
@media (max-width: 900px) {
  .test-body {
    flex-direction: column;
    overflow-y: auto;
  }

  .sidebar,
  .main-content {
    width: 100%;
    overflow: visible;
  }

  .sidebar {
    order: 2;
    /* Put questions at bottom on mobile? Or stick to top. Let's keep structure */
    max-height: 300px;
  }
}
</style>
