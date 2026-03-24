<template>
  <div class="container-fluid py-4">
    <!-- Loading State -->
    <div v-if="loading" class="text-center">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Đang tải...</span>
      </div>
    </div>

    <!-- Test Header with Timer -->
    <div v-else-if="!submitted">
      <div class="row mb-4">
        <div class="col-md-8">
          <h1>{{ currentTest.ten_bai_test }}</h1>
          <p class="text-muted">{{ currentTest.mo_ta }}</p>
        </div>
        <div class="col-md-4 text-end">
          <!-- Timer -->
          <div class="alert" :class="timerClass">
            <div class="fs-5">
              <i class="fa fa-clock"></i> {{ formattedTime }}
            </div>
            <small>Thời gian còn lại</small>
          </div>
        </div>
      </div>

      <!-- Questions Form -->
      <form @submit.prevent="onSubmitTest">
        <div 
          v-for="(question, questionIndex) in shuffledQuestions"
          :key="question.id"
          class="card mb-3"
        >
          <!-- Question Header -->
          <div class="card-header bg-info text-white">
            <h5 class="mb-0">
              Câu {{ questionIndex + 1 }} / {{ shuffledQuestions.length }}
              <span class="float-end badge bg-warning">{{ question.diem_toi_da }} điểm</span>
            </h5>
          </div>

          <div class="card-body">
            <!-- Question Content -->
            <div class="mb-3">
              <h6 class="mb-2">{{ question.noi_dung }}</h6>
              <img 
                v-if="question.hinh_anh_url"
                :src="question.hinh_anh_url"
                class="img-fluid mb-3"
                style="max-width: 400px"
              >
            </div>

            <!-- Answers - Multiple Choice -->
            <div v-if="question.loai_cau_hoi === 'multiple_choice'" class="mb-3">
              <div 
                v-for="(answer, index) in question.answers"
                :key="index"
                class="form-check mb-2"
              >
                <input 
                  :id="`answer_${questionIndex}_${index}`"
                  v-model="studentAnswers[questionIndex]"
                  :value="answer.id || index"
                  type="radio" 
                  class="form-check-input"
                  :name="`question_${questionIndex}`"
                >
                <label 
                  :for="`answer_${questionIndex}_${index}`"
                  class="form-check-label"
                >
                  {{ answer.noi_dung }}
                </label>
              </div>
            </div>

            <!-- Answers - True/False -->
            <div v-else-if="question.loai_cau_hoi === 'true_false'" class="mb-3">
              <div class="form-check mb-2">
                <input 
                  :id="`true_${questionIndex}`"
                  v-model="studentAnswers[questionIndex]"
                  value="true"
                  type="radio" 
                  class="form-check-input"
                  :name="`question_tf_${questionIndex}`"
                >
                <label :for="`true_${questionIndex}`" class="form-check-label">
                  ✓ Đúng
                </label>
              </div>
              <div class="form-check">
                <input 
                  :id="`false_${questionIndex}`"
                  v-model="studentAnswers[questionIndex]"
                  value="false"
                  type="radio" 
                  class="form-check-input"
                  :name="`question_tf_${questionIndex}`"
                >
                <label :for="`false_${questionIndex}`" class="form-check-label">
                  ✗ Sai
                </label>
              </div>
            </div>

            <!-- Answers - Image Choice -->
            <div v-else-if="question.loai_cau_hoi === 'image_choice'" class="mb-3">
              <div class="row">
                <div 
                  v-for="(answer, index) in question.answers"
                  :key="index"
                  class="col-md-4 mb-2"
                >
                  <label class="position-relative">
                    <input 
                      :id="`img_answer_${questionIndex}_${index}`"
                      v-model="studentAnswers[questionIndex]"
                      :value="answer.id || index"
                      type="radio" 
                      class="form-check-input position-absolute"
                      style="top: 10px; left: 10px;"
                    >
                    <img 
                      :src="answer.hinh_anh_url"
                      class="img-thumbnail w-100 cursor-pointer"
                      style="cursor: pointer;"
                      :style="{
                        border: studentAnswers[questionIndex] === String(answer.id || index) 
                          ? '3px solid #007bff' 
                          : '1px solid #ddd'
                      }"
                    >
                  </label>
                </div>
              </div>
            </div>

            <!-- Answers - Matching -->
            <div v-else-if="question.loai_cau_hoi === 'matching'" class="mb-3">
              <div class="row">
                <div class="col-md-6">
                  <h6>Cột Trái</h6>
                  <div 
                    v-for="(item, index) in question.answers"
                    :key="`left_${index}`"
                    class="mb-2"
                  >
                    <span class="badge bg-secondary">{{ item.trai }}</span>
                  </div>
                </div>
                <div class="col-md-6">
                  <h6>Ghép Vào Cột Phải</h6>
                  <div 
                    v-for="(item, index) in question.answers"
                    :key="`right_${index}`"
                    class="mb-2"
                  >
                    <select 
                      v-model="studentAnswers[questionIndex][index]"
                      class="form-select form-select-sm"
                    >
                      <option value="">-- Chọn --</option>
                      <option 
                        v-for="(answer, idx) in question.answers"
                        :key="`opt_${idx}`"
                        :value="idx"
                      >
                        {{ answer.phai }}
                      </option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Answers - Fill Blank -->
            <div v-else-if="question.loai_cau_hoi === 'fill_blank'" class="mb-3">
              <input 
                v-model="studentAnswers[questionIndex]"
                type="text" 
                class="form-control"
                placeholder="Nhập câu trả lời"
              >
            </div>

            <!-- Answers - Essay -->
            <div v-else-if="question.loai_cau_hoi === 'essay'" class="mb-3">
              <textarea 
                v-model="studentAnswers[questionIndex]"
                class="form-control"
                rows="4"
                placeholder="Viết câu trả lời của bạn tại đây..."
              ></textarea>
            </div>

            <!-- Question Description -->
            <div v-if="question.mo_ta_chi_tiet" class="alert alert-light mt-3 mb-0">
              <small class="text-muted">
                <strong>Ghi chú:</strong> {{ question.mo_ta_chi_tiet }}
              </small>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="d-grid gap-2 mb-4">
          <button 
            type="submit"
            class="btn btn-lg btn-success"
            :disabled="loading"
          >
            <i class="fa fa-check"></i> Nộp Bài Test
          </button>
        </div>
      </form>
    </div>

    <!-- Success State -->
    <div v-else class="alert alert-success text-center py-5">
      <h2>✓ Nộp Bài Thành Công!</h2>
      <p class="mb-0 mt-3">Bài test của bạn đã được ghi nhận. Hãy xem kết quả của bạn.</p>
      <router-link 
        :to="`/tests/${testId}/result`"
        class="btn btn-primary mt-3"
      >
        Xem Kết Quả
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useTestStore } from "@/stores/test";

const route = useRoute();
const router = useRouter();
const testStore = useTestStore();

// State
const testId = ref(route.params.id);
const currentTest = ref({});
const questions = ref([]);
const studentAnswers = ref([]);
const submitted = ref(false);
const timeRemaining = ref(0);
const timerInterval = ref(null);

// Computed
const loading = computed(() => testStore.loading);

const shuffledQuestions = computed(() => {
  if (currentTest.value.tro_luc_cau_hoi) {
    return [...questions.value].sort(() => Math.random() - 0.5);
  }
  return questions.value;
});

const formattedTime = computed(() => {
  const minutes = Math.floor(timeRemaining.value / 60);
  const seconds = timeRemaining.value % 60;
  return `${minutes}:${seconds.toString().padStart(2, "0")}`;
});

const timerClass = computed(() => {
  if (timeRemaining.value <= 60) {
    return "alert-danger";
  } else if (timeRemaining.value <= 300) {
    return "alert-warning";
  }
  return "alert-info";
});

// Methods
const initializeAnswers = () => {
  studentAnswers.value = questions.value.map((question) => {
    if (question.loai_cau_hoi === "matching") {
      return Array(question.answers.length).fill("");
    }
    return "";
  });
};

const startTimer = () => {
  timerInterval.value = setInterval(() => {
    timeRemaining.value--;
    if (timeRemaining.value <= 0) {
      clearInterval(timerInterval.value);
      onSubmitTest();
    }
  }, 1000);
};

const onSubmitTest = async () => {
  // Check for unanswered questions
  const unansweredCount = studentAnswers.value.filter((a, i) => {
    if (Array.isArray(a)) return a.some(v => v === '' || v === null);
    return a === '' || a === null || a === undefined;
  }).length;

  if (unansweredCount > 0) {
    if (!confirm(`Còn ${unansweredCount} câu hỏi chưa được trả lời. Bạn có muốn xác nhận nộp bài?`)) {
      return;
    }
  } else {
    if (!confirm('Bạn có chắc chắn muốn nộp bài?')) {
      return;
    }
  }

  try {
    const answers = studentAnswers.value.map((answer, index) => ({
      id_cau_hoi: questions.value[index].id,
      id_dap_an: typeof answer === 'number' ? answer : null,
      cau_tra_loi_tu_do: typeof answer === 'string' ? answer : null,
    })).filter(a => a.id_dap_an || a.cau_tra_loi_tu_do);

    await testStore.submitTest(testId.value, answers);

    submitted.value = true;
    clearInterval(timerInterval.value);
  } catch (err) {
    alert("Lỗi nộp bài: " + testStore.error);
  }
};

const loadTest = async () => {
  try {
    const test = await testStore.fetchTestDetail(testId.value);
    currentTest.value = test;
    questions.value = test.questions || [];
    initializeAnswers();
    
    // Initialize timer
    timeRemaining.value = test.thoi_gian_toi_da * 60; // Convert to seconds
    startTimer();
  } catch (err) {
    alert("Lỗi tải bài test: " + testStore.error);
    router.back();
  }
};

// Lifecycle
onMounted(() => {
  loadTest();
});

onUnmounted(() => {
  if (timerInterval.value) {
    clearInterval(timerInterval.value);
  }
});
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}

.form-check-input {
  cursor: pointer;
}

.card {
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.card-header {
  border-bottom: 2px solid rgba(255, 255, 255, 0.1);
}

.img-thumbnail:hover {
  opacity: 0.8;
}
</style>
