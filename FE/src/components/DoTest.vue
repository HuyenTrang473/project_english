<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { getDetail, startTest, submitTest as submitTestApi } from '@/api/testApi';

const route = useRoute();
const router = useRouter();

// --- State ---
const testId = computed(() => route.params.testId);
const testName = ref('');
const testDescription = ref('');
const loaiQuiz = ref('');
const timeLeft = ref(0);
const currentQuestionIndex = ref(0);
const questions = ref([]);
const loading = ref(true);
const error = ref('');
const submitting = ref(false);
const isPlayingAudio = ref(false);
const audioPlayerRef = ref(null);
const audioDuration = ref(0);
const audioCurrentTime = ref(0);
const audioLoading = ref(false);
const audioError = ref('');

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8000/api';
const API_ORIGIN = new URL(API_BASE_URL).origin;

const normalizeAudioUrl = (audioUrl) => {
  if (!audioUrl || typeof audioUrl !== 'string') return null;

  const trimmed = audioUrl.trim();
  if (!trimmed || trimmed.startsWith('blob:')) return null;

  if (trimmed.startsWith('/storage/')) {
    return `${API_ORIGIN}${trimmed}`;
  }

  if (trimmed.startsWith('storage/')) {
    return `${API_ORIGIN}/${trimmed}`;
  }

  if (/^https?:\/\//i.test(trimmed)) {
    try {
      const parsed = new URL(trimmed);
      if (parsed.pathname.startsWith('/storage/')) {
        return `${API_ORIGIN}${parsed.pathname}`;
      }
    } catch {
      // Keep original URL when parsing fails.
    }
  }

  return trimmed;
};

const resetAudioState = () => {
  isPlayingAudio.value = false;
  audioDuration.value = 0;
  audioCurrentTime.value = 0;
  audioError.value = '';
  const currentQuestion = questions.value[currentQuestionIndex.value];
  audioLoading.value = !!currentQuestion?.audio_url;
};

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
  if (timerInterval) clearInterval(timerInterval);
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
    const data = res?.data ?? res;

    console.log('API Response:', data); // Debug log

    testName.value = data.ten_bai_test || data.testName || '';
    testDescription.value = data.mo_ta || data.description || '';
    loaiQuiz.value = data.loai_quiz || 'mixed'; // Load quiz type
    timeLeft.value = (data.thoi_gian_toi_da || data.maxTime || 60) * 60; // convert minutes to seconds

    questions.value = (data.questions || []).map((q, idx) => {
      const questionObj = {
        id: q.id,
        noi_dung: q.noi_dung || q.content || '',
        loai_cau_hoi: q.loai_cau_hoi || q.type || 'multiple_choice',
        audio_url: normalizeAudioUrl(q.audio_url),
        audio_file_name: q.audio_file_name || null,
        audio_file_size: q.audio_file_size || null,
        hinh_anh_url: q.hinh_anh_url || null,
        answers: (q.answers || []).map(a => ({
          id: a.id,
          noi_dung: a.noi_dung || a.content || '',
        })),
        userAnswer: null,
      };
      console.log(`Question ${idx}:`, questionObj); // Debug each question
      return questionObj;
    });

    console.log('All questions loaded:', questions.value);
    resetAudioState();

    // Register the test attempt
    const startRes = await startTest(testId.value);
    const startPayload = startRes?.data ?? startRes;
    const attemptData = startPayload?.data ?? {};

    if (typeof attemptData.thoi_gian_con_lai === 'number') {
      timeLeft.value = Math.max(0, attemptData.thoi_gian_con_lai);
    } else if (attemptData.thoi_gian_bat_dau && attemptData.thoi_gian_toi_da) {
      const startedAt = new Date(attemptData.thoi_gian_bat_dau).getTime();
      const nowMs = Date.now();
      const elapsedSeconds = Math.max(0, Math.floor((nowMs - startedAt) / 1000));
      const maxSeconds = Number(attemptData.thoi_gian_toi_da) * 60;
      timeLeft.value = Math.max(0, maxSeconds - elapsedSeconds);
    }

    startTimerCountdown();
  } catch (err) {
    const msg = err.response?.data?.message || err.message;
    error.value = msg || 'Không thể tải bài test';
    console.error('Error loading test:', err);
  } finally {
    loading.value = false;
  }
};

const playAudio = () => {
  const currentQuestion = questions.value[currentQuestionIndex.value];
  if (currentQuestion.audio_url && audioPlayerRef.value) {
    // Stop any other audio that might be playing
    stopAllAudio();

    // Now play the current audio
    audioPlayerRef.value.play().catch((err) => {
      console.error('Error playing audio:', err);
      audioError.value = 'Không thể phát âm thanh. Vui lòng thử lại.';
    });
    isPlayingAudio.value = true;
  }
};

const stopAudio = () => {
  if (audioPlayerRef.value) {
    audioPlayerRef.value.pause();
    audioPlayerRef.value.currentTime = 0;
    audioCurrentTime.value = 0;
    isPlayingAudio.value = false;
  }
};

const seekAudio = (event) => {
  if (audioPlayerRef.value) {
    audioPlayerRef.value.currentTime = event.target.value;
  }
};

const updateAudioTime = () => {
  if (audioPlayerRef.value) {
    audioCurrentTime.value = audioPlayerRef.value.currentTime;
  }
};

const onAudioLoaded = () => {
  if (audioPlayerRef.value) {
    audioDuration.value = audioPlayerRef.value.duration;
    audioLoading.value = false;
    audioError.value = '';
  }
};

const onAudioError = () => {
  audioLoading.value = false;
  audioError.value = 'Không thể tải file âm thanh. Vui lòng kiểm tra kết nối.';
};

const formatTime = (seconds) => {
  if (!seconds || isNaN(seconds)) return '0:00';
  const minutes = Math.floor(seconds / 60);
  const secs = Math.floor(seconds % 60);
  return `${minutes}:${secs.toString().padStart(2, '0')}`;
};

const stopAllAudio = () => {
  // Find and pause all audio elements on the page
  const allAudioElements = document.querySelectorAll('audio');
  allAudioElements.forEach(audio => {
    if (!audio.paused) {
      audio.pause();
      audio.currentTime = 0;
    }
  });
  isPlayingAudio.value = false;
};

onMounted(() => {
  loadTest();
});

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval);
  stopAllAudio(); // Stop any audio when component unmounts
});

// --- Actions ---
const selectQuestion = (index) => {
  currentQuestionIndex.value = index;
};

watch(currentQuestionIndex, () => {
  stopAllAudio();
  resetAudioState();
});

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
    const score = res?.data?.diem_tong ?? res?.diem_tong ?? 'Chưa chấm';
    alert('Hoàn thành! Điểm của bạn: ' + score);
    router.push({ name: 'TestResult', params: { id: testId.value } });
  } catch (err) {
    alert('Lỗi khi nộp bài: ' + (err.response?.data?.message || err.message));
  } finally {
    submitting.value = false;
  }
};

const isCurrent = (index) => currentQuestionIndex.value === index;
const isAnswered = (question) => question.userAnswer !== null;

// Calculate optimal grid columns based on number of questions
const gridColumns = computed(() => {
  const totalQuestions = questions.value.length;
  if (totalQuestions <= 5) return 5; // 1 row
  if (totalQuestions <= 10) return 5; // 2 rows
  if (totalQuestions <= 20) return 6; // 3-4 rows
  if (totalQuestions <= 40) return 8; // 5 rows
  return 10; // 4+ items per row for large tests
});
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
      <h2><i class="fa-solid fa-triangle-exclamation icon-inline" aria-hidden="true"></i> Không thể tải bài test</h2>
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
            <span class="timer-icon"><i class="fa-regular fa-clock" aria-hidden="true"></i></span>
            {{ formattedTime }}
          </div>

          <div class="questions-grid" :style="{ gridTemplateColumns: `repeat(${gridColumns}, 1fr)` }">
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

              <!-- Audio Player for Listening Quizzes -->
              <div v-if="loaiQuiz === 'listening'" class="listening-section">
                <div v-if="questions[currentQuestionIndex].audio_url" class="audio-player-container">
                  <!-- Audio Header -->
                  <div class="audio-header">
                    <div class="audio-icon"><i class="fa-solid fa-headphones" aria-hidden="true"></i></div>
                    <div class="audio-info">
                      <h4>Phần Nghe</h4>
                      <p v-if="questions[currentQuestionIndex].audio_file_name" class="audio-filename">
                        {{ questions[currentQuestionIndex].audio_file_name }}
                      </p>
                    </div>
                  </div>

                  <!-- Native Audio Player -->
                  <div class="audio-player-native">
                    <audio
                      :key="`audio-${questions[currentQuestionIndex].id}-${questions[currentQuestionIndex].audio_url || 'none'}`"
                      ref="audioPlayerRef" :src="questions[currentQuestionIndex].audio_url"
                      @ended="isPlayingAudio = false" @timeupdate="updateAudioTime" @loadedmetadata="onAudioLoaded"
                      @error="onAudioError" controls>
                      Your browser does not support the audio element.
                    </audio>
                  </div>

                  <!-- Custom Controls -->
                  <div class="audio-controls">
                    <button @click="playAudio" class="audio-btn-play" :disabled="isPlayingAudio">
                      <span v-if="!isPlayingAudio"><i class="fa-solid fa-play" aria-hidden="true"></i> Phát</span>
                      <span v-else><i class="fa-solid fa-pause" aria-hidden="true"></i> Dừng</span>
                    </button>
                    <button @click="stopAudio" class="audio-btn-stop">
                      <i class="fa-solid fa-stop" aria-hidden="true"></i> Dừng Lại
                    </button>
                    <button @click="playAudio" class="audio-btn-repeat" title="Phát Lại">
                      <i class="fa-solid fa-rotate-right" aria-hidden="true"></i> Phát Lại
                    </button>
                    <div class="audio-duration" v-if="audioDuration">
                      {{ formatTime(audioCurrentTime) }} / {{ formatTime(audioDuration) }}
                    </div>
                  </div>

                  <!-- Progress Bar -->
                  <div class="audio-progress">
                    <input v-if="audioDuration" type="range" :value="audioCurrentTime" :max="audioDuration"
                      @input="seekAudio" class="progress-slider">
                    <div v-else class="progress-loading">
                      <i class="fa-solid fa-hourglass-half" aria-hidden="true"></i> Đang tải âm thanh...
                    </div>
                  </div>

                  <!-- Audio Status -->
                  <div class="audio-status">
                    <span v-if="audioLoading" class="status-badge loading"><i class="fa-solid fa-hourglass-half"
                        aria-hidden="true"></i> Đang tải...</span>
                    <span v-else-if="audioError" class="status-badge error"><i class="fa-solid fa-circle-xmark"
                        aria-hidden="true"></i> Không thể tải</span>
                    <span v-else-if="isPlayingAudio" class="status-badge playing"><i class="fa-solid fa-volume-high"
                        aria-hidden="true"></i> Đang phát</span>
                    <span v-else class="status-badge ready"><i class="fa-solid fa-check" aria-hidden="true"></i> Sẵn
                      sàng</span>
                  </div>
                </div>

                <!-- No Audio Message -->
                <div v-else class="no-audio-message">
                  <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    Câu hỏi này chưa có file audio. Vui lòng liên hệ giáo viên.
                  </div>
                </div>
              </div>

              <p class="question-text">{{ questions[currentQuestionIndex].noi_dung }}</p>

              <div class="options-list">
                <label v-for="ans in questions[currentQuestionIndex].answers" :key="ans.id" class="option-label"
                  :class="{ 'selected': questions[currentQuestionIndex].userAnswer === ans.id }">
                  <input type="radio" :name="`q-${questions[currentQuestionIndex].id}`" :value="ans.id"
                    :checked="questions[currentQuestionIndex].userAnswer === ans.id" @change="answerQuestion(ans.id)">
                  <span class="option-text">{{ ans.noi_dung }}</span>
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

.icon-inline {
  margin-right: 0.35rem;
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
  gap: 10px;
  transition: grid-template-columns 0.3s ease;
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
  background-color: #ffc0e0;
  /* Light Pink */
  color: #d6348f;
  border: 1px solid #d6348f;
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
  background: #ffc0e0;
  border-color: #d6348f;
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

/* Audio Player */
.audio-section {
  display: flex;
  gap: 1rem;
  align-items: center;
  padding: 1rem;
  background: #f0f8ff;
  border: 1px solid #87ceeb;
  border-radius: 12px;
  margin-bottom: 1.5rem;
}

.audio-section audio {
  flex: 1;
  max-width: 300px;
  height: 30px;
  accent-color: #fc74dd;
}

.audio-btn {
  padding: 0.6rem 1.2rem;
  background: #fc74dd;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.9rem;
  transition: all 0.2s;
  white-space: nowrap;
}

.audio-btn:hover:not(:disabled) {
  background: #ff4d9e;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(252, 116, 221, 0.4);
}

.audio-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* ===== LISTENING SECTION STYLES ===== */
.listening-section {
  margin-bottom: 2rem;
  padding-top: 1rem;
  border-top: 2px solid #f0f0f0;
}

.audio-player-container {
  background: linear-gradient(135deg, #fff7fc 0%, #f5e8ff 100%);
  border: 2px solid #fc74dd;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 8px 32px rgba(252, 116, 221, 0.15);
}

/* Audio Header */
.audio-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.audio-icon {
  font-size: 2rem;
  animation: bounce 2s infinite;
}

.audio-controls button i,
.progress-loading i,
.status-badge i {
  margin-right: 0.35rem;
}

@keyframes bounce {

  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-5px);
  }
}

.audio-info h4 {
  margin: 0;
  color: #333;
  font-size: 1rem;
  font-weight: 700;
}

.audio-filename {
  margin: 0.3rem 0 0 0;
  color: #666;
  font-size: 0.85rem;
  word-break: break-word;
}

/* Native Audio Player */
.audio-player-native {
  margin-bottom: 1rem;
  width: 100%;
}

.audio-player-native audio {
  width: 100%;
  height: 50px;
  border-radius: 10px;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(252, 116, 221, 0.1);
}

/* Custom Audio Controls */
.audio-controls {
  display: flex;
  gap: 0.8rem;
  align-items: center;
  flex-wrap: wrap;
  margin-bottom: 1rem;
}

.audio-btn-play,
.audio-btn-stop,
.audio-btn-repeat {
  padding: 0.65rem 1.2rem;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
  white-space: nowrap;
  flex: 1;
  min-width: 100px;
  max-width: 150px;
}

.audio-btn-play {
  background: linear-gradient(135deg, #fc74dd 0%, #ff4d9e 100%);
  color: white;
  box-shadow: 0 4px 15px rgba(252, 116, 221, 0.3);
}

.audio-btn-play:hover:not(:disabled) {
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(252, 116, 221, 0.5);
}

.audio-btn-stop {
  background: #ff6b6b;
  color: white;
  box-shadow: 0 4px 15px rgba(255, 107, 107, 0.2);
}

.audio-btn-stop:hover {
  background: #ff5252;
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
}

.audio-btn-repeat {
  background: #4ecdc4;
  color: white;
  box-shadow: 0 4px 15px rgba(78, 205, 196, 0.2);
}

.audio-btn-repeat:hover {
  background: #3bb9af;
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(78, 205, 196, 0.4);
}

.audio-btn-play:disabled,
.audio-btn-stop:disabled,
.audio-btn-repeat:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.audio-duration {
  padding: 0.5rem 1rem;
  background: white;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 600;
  color: #666;
  border: 1px solid #e0e0e0;
  min-width: 100px;
  text-align: center;
}

/* Progress Bar */
.audio-progress {
  margin-bottom: 1rem;
}

.progress-slider {
  width: 100%;
  height: 8px;
  border-radius: 10px;
  background: #ddd;
  outline: none;
  -webkit-appearance: none;
  appearance: none;
  cursor: pointer;
}

.progress-slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: #fc74dd;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(252, 116, 221, 0.4);
  transition: all 0.2s;
}

.progress-slider::-webkit-slider-thumb:hover {
  transform: scale(1.2);
  box-shadow: 0 4px 12px rgba(252, 116, 221, 0.6);
}

.progress-slider::-moz-range-thumb {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: #fc74dd;
  cursor: pointer;
  border: none;
  box-shadow: 0 2px 8px rgba(252, 116, 221, 0.4);
  transition: all 0.2s;
}

.progress-slider::-moz-range-thumb:hover {
  transform: scale(1.2);
  box-shadow: 0 4px 12px rgba(252, 116, 221, 0.6);
}

.progress-loading {
  padding: 0.8rem;
  text-align: center;
  color: #999;
  font-size: 0.9rem;
  animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {

  0%,
  100% {
    opacity: 1;
  }

  50% {
    opacity: 0.6;
  }
}

/* Audio Status Badge */
.audio-status {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
}

.status-badge {
  display: inline-block;
  padding: 0.4rem 1rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
}

.status-badge.loading {
  background: #fff3cd;
  color: #856404;
  animation: pulse 1s ease-in-out infinite;
}

.status-badge.error {
  background: #f8d7da;
  color: #721c24;
}

.status-badge.playing {
  background: #d1ecf1;
  color: #0c5460;
  animation: pulse-blue 1s ease-in-out infinite;
}

@keyframes pulse-blue {

  0%,
  100% {
    opacity: 1;
  }

  50% {
    opacity: 0.7;
  }
}

.status-badge.ready {
  background: #d4edda;
  color: #155724;
}

/* No Audio Message */
.no-audio-message {
  margin-bottom: 2rem;
}

.no-audio-message .alert {
  background: #fff3cd;
  border: 1px solid #ffc107;
  border-radius: 12px;
  padding: 1rem;
  color: #856404;
  display: flex;
  align-items: center;
  gap: 0.8rem;
}

/* Responsive audio controls */
@media (max-width: 768px) {
  .audio-controls {
    gap: 0.5rem;
  }

  .audio-btn-play,
  .audio-btn-stop,
  .audio-btn-repeat {
    flex: 1;
    min-width: 80px;
    max-width: none;
    font-size: 0.85rem;
    padding: 0.5rem 0.8rem;
  }

  .audio-duration {
    min-width: 90px;
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
  }
}

@media (max-width: 600px) {
  .audio-player-container {
    padding: 1rem;
  }

  .audio-header {
    gap: 0.8rem;
    margin-bottom: 1rem;
  }

  .audio-icon {
    font-size: 1.5rem;
  }

  .audio-controls {
    gap: 0.3rem;
  }

  .audio-btn-play,
  .audio-btn-stop,
  .audio-btn-repeat {
    font-size: 0.75rem;
    padding: 0.5rem 0.5rem;
  }

  .audio-info h4 {
    font-size: 0.95rem;
  }

  .audio-filename {
    font-size: 0.8rem;
  }
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
