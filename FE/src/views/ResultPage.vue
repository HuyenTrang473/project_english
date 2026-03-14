<template>
  <div class="container-fluid py-4">
    <!-- Loading State -->
    <div v-if="loading" class="text-center">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Đang tải...</span>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="alert alert-danger">
      {{ error }}
    </div>

    <!-- Results Display -->
    <div v-else>
      <!-- Header -->
      <div class="text-center mb-5">
        <h1>📊 Kết Quả Bài Test</h1>
        <p class="text-muted">{{ result.ten_bai_test }}</p>
      </div>

      <!-- Score Card -->
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="card text-center">
            <div class="card-body py-5">
              <h2 class="display-1" :style="{ color: scoreColor }">
                {{ result.diem_thi }}
              </h2>
              <h5 class="text-muted mb-3">Điểm Của Bạn</h5>
              <div class="progress mb-3" style="height: 25px;">
                <div 
                  class="progress-bar"
                  :style="{ 
                    width: scorePercentage + '%',
                    backgroundColor: scoreColor
                  }"
                >
                  {{ scorePercentage }}%
                </div>
              </div>
              <p class="mb-0">
                <span 
                  :class="[
                    'badge badge-lg',
                    isPassed ? 'bg-success' : 'bg-danger'
                  ]"
                >
                  {{ isPassed ? '✓ ĐẠT' : '✗ KHÔNG ĐẠT' }}
                </span>
              </p>
            </div>
          </div>
        </div>

        <!-- Score Details -->
        <div class="col-md-6">
          <div class="card">
            <div class="card-header bg-info text-white">
              <h5 class="mb-0">📈 Chi Tiết</h5>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <p class="mb-1"><strong>Bài Test:</strong> {{ result.ten_bai_test }}</p>
                <p class="mb-1"><strong>Lần Làm:</strong> Lần #{{ result.lan_thu }}</p>
                <p class="mb-1"><strong>Thời Gian Làm:</strong> {{ result.thoi_gian_lam_tot }} phút</p>
                <p class="mb-1"><strong>Ngày Nộp:</strong> {{ formatDate(result.ngay_nop) }}</p>
              </div>
              <div class="border-top pt-3">
                <p class="mb-1">
                  <strong>Điểm Tối Đa:</strong> {{ result.diem_tong_max }}
                </p>
                <p class="mb-1">
                  <strong>Điểm Đạt:</strong> {{ result.diem_dat }}
                </p>
                <p class="mb-0">
                  <strong>Trạng Thái:</strong>
                  <span 
                    :class="[
                      'badge',
                      result.trang_thai === 'completed' ? 'bg-success' : 'bg-warning'
                    ]"
                  >
                    {{ statusText }}
                  </span>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Questions Review -->
      <div class="card mb-4">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">❓ Chi Tiết Từng Câu</h5>
        </div>
        <div class="card-body">
          <div 
            v-for="(question, index) in questions"
            :key="question.id"
            class="mb-4 pb-4 border-bottom"
          >
            <!-- Question -->
            <div class="mb-3">
              <h6 class="mb-2">
                <span class="badge bg-secondary">Câu {{ index + 1 }}</span>
                <span class="ms-2">{{ question.noi_dung }}</span>
              </h6>
              <img 
                v-if="question.hinh_anh_url"
                :src="question.hinh_anh_url"
                class="img-thumbnail mb-2"
                style="max-width: 300px"
              >
            </div>

            <!-- Student Answer -->
            <div class="mb-2">
              <p class="mb-1">
                <strong><i class="fa fa-user"></i> Câu Trả Lời Của Bạn:</strong>
              </p>
              <div 
                class="alert"
                :class="{
                  'alert-success': isAnswerCorrect(question, index),
                  'alert-danger': !isAnswerCorrect(question, index)
                }"
              >
                {{ getAnswerDisplay(question, index) }}
              </div>
            </div>

            <!-- Correct Answer (if show_answers setting enabled) -->
            <div v-if="shouldShowAnswers(question, index)" class="mb-2">
              <p class="mb-1">
                <strong><i class="fa fa-check"></i> Đáp Án Đúng:</strong>
              </p>
              <div class="alert alert-success">
                {{ getCorrectAnswerDisplay(question) }}
              </div>
            </div>

            <!-- Score -->
            <div class="mb-0">
              <span class="badge bg-info">
                {{ getQuestionScore(index) }} / {{ question.diem_toi_da }} điểm
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="d-flex gap-2 mb-4">
        <!-- Retake Button -->
        <button 
          v-if="canRetake && result.lan_thu < result.so_lan_lam_toi_da"
          @click="onRetake"
          class="btn btn-primary"
          :disabled="loading"
        >
          <i class="fa fa-redo"></i> Làm Lại
          <span class="badge bg-secondary ms-2">
            {{ result.so_lan_lam_toi_da - result.lan_thu }} lần còn lại
          </span>
        </button>

        <!-- Back Button -->
        <router-link 
          to="/tests"
          class="btn btn-outline-secondary"
        >
          <i class="fa fa-arrow-left"></i> Quay Lại Danh Sách
        </router-link>

        <!-- Print Button -->
        <button 
          @click="onPrint"
          class="btn btn-outline-info"
        >
          <i class="fa fa-print"></i> In Kết Quả
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useTestStore } from "@/stores/test";

const route = useRoute();
const router = useRouter();
const testStore = useTestStore();

// State
const testId = ref(route.params.id);
const result = ref({});
const questions = ref([]);
const studentAnswers = ref([]);
const error = ref("");

// Computed
const loading = computed(() => testStore.loading);

const scorePercentage = computed(() => {
  return Math.round((result.value.diem_thi / result.value.diem_tong_max) * 100);
});

const scoreColor = computed(() => {
  const percentage = scorePercentage.value;
  if (percentage >= 80) return "#28a745"; // Green
  if (percentage >= 60) return "#ffc107"; // Yellow
  return "#dc3545"; // Red
});

const isPassed = computed(() => {
  return result.value.diem_thi >= result.value.diem_dat;
});

const statusText = computed(() => {
  const status = {
    not_started: "Chưa làm",
    in_progress: "Đang làm",
    completed: "Hoàn thành",
    pending_review: "Chờ chấm điểm",
    graded: "Đã chấm",
  };
  return status[result.value.trang_thai] || result.value.trang_thai;
});

const canRetake = computed(() => {
  return result.value.so_lan_lam_toi_da > 1;
});

// Methods
const formatDate = (date) => {
  if (!date) return "";
  return new Date(date).toLocaleString("vi-VN");
};

const isAnswerCorrect = (question, index) => {
  const studentAnswer = studentAnswers.value[index];
  
  if (question.loai_cau_hoi === "essay") {
    // Essays are always pending manual grading
    return result.value.trang_thai === "graded" && studentAnswer?.diem_phan_thi > 0;
  }

  // Check if student answer matches correct answer
  const correctAnswer = question.answers.find(a => a.la_dap_an_dung);
  return String(studentAnswer?.dap_an_hoc_sinh) === String(correctAnswer?.id);
};

const getAnswerDisplay = (question, index) => {
  const studentAnswer = studentAnswers.value[index];
  
  if (!studentAnswer) return "Không có câu trả lời";

  if (question.loai_cau_hoi === "matching") {
    return studentAnswer.dap_an_hoc_sinh
      .map((idx, i) => `${question.answers[i].trai} → ${question.answers[idx]?.phai || "?"}`)
      .join("; ");
  }

  if (question.loai_cau_hoi === "multiple_choice" || question.loai_cau_hoi === "image_choice") {
    const answer = question.answers[parseInt(studentAnswer.dap_an_hoc_sinh)];
    return answer ? answer.noi_dung : "Không có câu trả lời";
  }

  if (question.loai_cau_hoi === "true_false") {
    return studentAnswer.dap_an_hoc_sinh === "true" ? "✓ Đúng" : "✗ Sai";
  }

  return studentAnswer.dap_an_hoc_sinh || "Không có câu trả lời";
};

const getCorrectAnswerDisplay = (question) => {
  if (question.loai_cau_hoi === "essay") {
    return question.dap_an_mau || "Chờ giáo viên chấm";
  }

  const correctAnswers = question.answers.filter(a => a.la_dap_an_dung);
  
  if (question.loai_cau_hoi === "matching") {
    return correctAnswers.map(a => `${a.trai} → ${a.phai}`).join("; ");
  }

  return correctAnswers.map(a => a.noi_dung).join("; ");
};

const shouldShowAnswers = (question, index) => {
  // Show if show_answers_immediately is true, or if result is graded
  return result.value.hien_thi_dap_an_dung || result.value.trang_thai === "graded";
};

const getQuestionScore = (index) => {
  const answer = studentAnswers.value[index];
  return answer?.diem_phan_thi || 0;
};

const onRetake = async () => {
  router.push(`/tests/${testId.value}/take`);
};

const onPrint = () => {
  window.print();
};

// Load result
const loadResult = async () => {
  try {
    const data = await testStore.fetchResult(testId.value);
    result.value = data.result;
    questions.value = data.questions || [];
    studentAnswers.value = data.student_answers || [];
  } catch (err) {
    error.value = testStore.error || "Lỗi tải kết quả";
  }
};

// Lifecycle
onMounted(() => {
  loadResult();
});
</script>

<style scoped>
.display-1 {
  font-weight: bold;
}

.badge-lg {
  font-size: 1rem;
  padding: 0.5rem 1rem;
}

.progress {
  border-radius: 10px;
}

.alert {
  border-left: 4px solid;
}

.alert-success {
  border-left-color: #28a745;
  background-color: #d4edda;
}

.alert-danger {
  border-left-color: #dc3545;
  background-color: #f8d7da;
}
</style>
