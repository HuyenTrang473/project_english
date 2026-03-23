<template>
  <div class="container-fluid py-4 bg-light" style="min-height: 100vh">
    <!-- Header with Breadcrumb -->
    <div class="mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h1 class="mb-0">{{ isEdit ? '✏️ Chỉnh Sửa Bài Test' : '➕ Tạo Bài Test Mới' }}</h1>
          <small class="text-muted">Xây dựng và quản lý bài test trắc nghiệm cho học sinh</small>
        </div>
        <button @click="onBack" class="btn btn-outline-secondary">
          <i class="fa fa-arrow-left"></i> Quay Lại
        </button>
      </div>
      <!-- Progress Indicator -->
      <div class="progress" style="height: 3px;">
        <div class="progress-bar bg-success" :style="{ width: progressPercent + '%' }"></div>
      </div>
      <!-- Template Indicator -->
      <div v-if="usingTemplate" class="alert alert-info mt-3 mb-0 d-flex align-items-center">
        <i class="fa fa-lightbulb-o me-2"></i>
        <span>Đang sử dụng template: <strong>{{ templateName }}</strong></span>
      </div>
    </div>

    <!-- Main Form -->
    <div class="row">
      <!-- Left Column - Test Settings -->
      <div class="col-lg-8">
        <div class="card mb-4">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0">⚙️ Cài Đặt Bài Test</h5>
          </div>
          <div class="card-body">
            <!-- Lesson Selection -->
            <div class="mb-3">
              <label class="form-label">Bài Học <span class="text-danger">*</span></label>
              <select v-model="form.id_lesson" :class="['form-select', { 'is-invalid': formErrors.id_lesson }]">
                <option value="">-- Chọn bài học --</option>
                <option v-for="lesson in lessons" :key="lesson.id" :value="lesson.id">
                  {{ lesson.tieu_de }}
                </option>
              </select>
              <div v-if="formErrors.id_lesson" class="invalid-feedback d-block">
                {{ formErrors.id_lesson }}
              </div>
            </div>

            <!-- Lesson Content Display -->
            <div v-if="selectedLesson" class="alert alert-info mb-3">
              <div class="d-flex align-items-start">
                <div class="me-3">
                  <i class="fa fa-book fa-2x text-info"></i>
                </div>
                <div class="flex-grow-1">
                  <h6 class="mb-2">📖 {{ selectedLesson.tieu_de }}</h6>
                  <p class="mb-1 small" v-if="selectedLesson.mo_ta">
                    <strong>Nội dung:</strong> {{ selectedLesson.mo_ta }}
                  </p>
                  <p class="mb-0 small text-muted" v-if="selectedLesson.loai_bai_hoc">
                    <strong>Loại:</strong> {{ selectedLesson.loai_bai_hoc }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Quiz Type Selection -->
            <div class="mb-3">
              <label class="form-label">Loại Quiz <span class="text-danger">*</span></label>
              <select v-model="form.loai_quiz" class="form-select">
                <option value="listening">🎧 Listening (Nghe)</option>
                <option value="writing">✍️ Writing (Viết)</option>
                <option value="reading">📖 Reading (Đọc)</option>
                <option value="mixed">🎯 Mixed (Hỗn Hợp - Test Năng Lực)</option>
              </select>
              <small class="form-text text-muted">
                Chọn loại quiz để tổ chức câu hỏi một cách hợp lý
              </small>
            </div>

            <!-- Quiz Type Details -->
            <div v-if="form.loai_quiz" class="mb-3">
              <label class="form-label">Mô Tả Chi Tiết Loại Quiz</label>
              <textarea v-model="form.chi_tiet_loai_quiz" class="form-control" rows="2"
                :placeholder="getQuizTypeDescription(form.loai_quiz)"></textarea>
            </div>

            <!-- Test Name -->
            <div class="mb-3">
              <label class="form-label">Tên Bài Test <span class="text-danger">*</span></label>
              <input v-model="form.ten_bai_test" type="text"
                :class="['form-control', { 'is-invalid': formErrors.ten_bai_test }]" placeholder="Nhập tên bài test">
              <div v-if="formErrors.ten_bai_test" class="invalid-feedback d-block">
                {{ formErrors.ten_bai_test }}
              </div>
            </div>

            <!-- Description -->
            <div class="mb-3">
              <label class="form-label">Mô Tả</label>
              <textarea v-model="form.mo_ta" class="form-control" rows="3"
                placeholder="Nhập mô tả cho bài test"></textarea>
            </div>

            <!-- Test Configuration Row 1 -->
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Thời Gian Tối Đa (phút) <span class="text-danger">*</span></label>
                <input v-model.number="form.thoi_gian_toi_da" type="number"
                  :class="['form-control', { 'is-invalid': formErrors.thoi_gian_toi_da }]" min="1" placeholder="60">
                <div v-if="formErrors.thoi_gian_toi_da" class="invalid-feedback d-block">
                  {{ formErrors.thoi_gian_toi_da }}
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Điểm Tối Đa <span class="text-danger">*</span></label>
                <input v-model.number="form.diem_tong_max" type="number"
                  :class="['form-control', { 'is-invalid': formErrors.diem_tong_max }]" min="1" placeholder="100">
                <div v-if="formErrors.diem_tong_max" class="invalid-feedback d-block">
                  {{ formErrors.diem_tong_max }}
                </div>
              </div>
            </div>

            <!-- Test Configuration Row 2 -->
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Số Lần Làm Tối Đa</label>
                <input v-model.number="form.so_lan_lam_toi_da" type="number" class="form-control" min="1"
                  placeholder="3">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Điểm Đạt (để pass)</label>
                <input v-model.number="form.diem_dat" type="number" class="form-control" placeholder="60">
              </div>
            </div>

            <!-- Checkboxes -->
            <div class="row">
              <div class="col-md-6 mb-3">
                <div class="form-check">
                  <input v-model="form.co_xao_tron_cau_hoi" type="checkbox" class="form-check-input"
                    id="shuffleQuestions">
                  <label class="form-check-label" for="shuffleQuestions">
                    Trộn Lẫn Câu Hỏi
                  </label>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <div class="form-check">
                  <input v-model="form.co_xao_tron_dap_an" type="checkbox" class="form-check-input" id="shuffleAnswers">
                  <label class="form-check-label" for="shuffleAnswers">
                    Trộn Lẫn Đáp Án
                  </label>
                </div>
              </div>
            </div>

            <!-- Results Display -->
            <div class="row">
              <div class="col-md-6 mb-3">
                <div class="form-check">
                  <input v-model="form.hien_thi_ket_qua_ngay_lap" type="checkbox" class="form-check-input"
                    id="showResultsImmediately">
                  <label class="form-check-label" for="showResultsImmediately">
                    Hiển Thị Kết Quả Ngay Lập Tức
                  </label>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <div class="form-check">
                  <input v-model="form.hien_thi_dap_an_dung" type="checkbox" class="form-check-input"
                    id="showCorrectAnswers">
                  <label class="form-check-label" for="showCorrectAnswers">
                    Hiển Thị Đáp Án Đúng
                  </label>
                </div>
              </div>
            </div>

            <!-- Status -->
            <div class="mb-3">
              <label class="form-label">Trạng Thái</label>
              <select v-model.number="form.trang_thai" class="form-select">
                <option :value="1">🗒️ Nháp</option>
                <option :value="2">📢 Công Bố</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column - Summary -->
      <div class="col-lg-4">
        <div class="card sticky-top" style="top: 20px;">
          <div class="card-header bg-info text-white">
            <h5 class="mb-0">📊 Tóm Tắt</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <p class="mb-2">
                <strong>Câu Hỏi:</strong> {{ questions.length }}
              </p>
              <p class="mb-2">
                <strong>Tên:</strong> {{ form.ten_bai_test || '(Chưa nhập)' }}
              </p>
              <p class="mb-2">
                <strong>Loại Quiz:</strong> {{ getQuizTypeLabel(form.loai_quiz) }}
              </p>
              <p class="mb-2">
                <strong>Thời Gian:</strong> {{ form.thoi_gian_toi_da || 0 }} phút
              </p>
              <p class="mb-2">
                <strong>Điểm Tối Đa:</strong> {{ form.diem_tong_max || 0 }} điểm
              </p>
              <div class="mt-3 pt-3 border-top">
                <div class="badge bg-primary">
                  {{ form.co_xao_tron_cau_hoi ? '✓' : '✗' }} Trộn Câu Hỏi
                </div>
                <div class="badge bg-primary ms-1">
                  {{ form.co_xao_tron_dap_an ? '✓' : '✗' }} Trộn Đáp Án
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Questions Section -->
    <div class="card mt-4 mb-4">
      <div class="card-header bg-success text-white">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0">❓ Câu Hỏi ({{ questions.length }})</h5>
          <button @click="openQuestionEditor()" class="btn btn-light btn-sm">
            <i class="fa fa-plus"></i> Thêm Câu Hỏi
          </button>
        </div>
      </div>
      <div class="card-body">
        <!-- Questions List -->
        <div v-if="questions.length === 0" class="alert alert-info mb-0">
          Chưa có câu hỏi nào. Hãy thêm câu hỏi vào bài test!
        </div>

        <div v-else class="list-group">
          <div v-for="(question, index) in questions" :key="question.id || index" class="list-group-item">
            <div class="d-flex justify-content-between align-items-start">
              <div class="flex-grow-1">
                <h6 class="mb-1">
                  <span class="badge bg-secondary">{{ index + 1 }}</span>
                  <span class="ms-2">[{{ question.loai_cau_hoi }}]</span>
                </h6>
                <p class="mb-1 text-muted small">{{ truncate(question.noi_dung, 100) }}</p>
                <small class="text-muted">
                  <i class="fa fa-star"></i> {{ question.diem_toi_da || 0 }} điểm
                </small>
              </div>
              <div class="btn-group btn-group-sm ms-3">
                <button @click="openQuestionEditor(question)" class="btn btn-warning">
                  <i class="fa fa-edit"></i>
                </button>
                <button @click="deleteQuestion(index)" class="btn btn-danger">
                  <i class="fa fa-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex gap-2 mb-4">
      <button @click="onSubmit" :disabled="loading" class="btn btn-primary">
        <i class="fa fa-save"></i> {{ isEdit ? 'Cập Nhật Bài Test' : 'Tạo Bài Test' }}
      </button>
      <button @click="onBack" class="btn btn-outline-secondary">
        <i class="fa fa-times"></i> Hủy
      </button>
    </div>

    <!-- Question Editor Modal -->
    <QuestionEditor v-if="showQuestionEditor" :question="selectedQuestion" :loai-quiz="form.loai_quiz"
      @save="onQuestionSave" @close="showQuestionEditor = false" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useTestStore } from "@/stores/test";
import QuestionEditor from "@/components/QuestionEditor.vue";

const router = useRouter();
const route = useRoute();
const testStore = useTestStore();

// State
const form = ref({
  id_lesson: "",
  ten_bai_test: "",
  loai_quiz: "mixed",
  chi_tiet_loai_quiz: "",
  mo_ta: "",
  thoi_gian_toi_da: 60,
  diem_tong_max: 100,
  so_lan_lam_toi_da: 3,
  diem_dat: 60,
  co_xao_tron_cau_hoi: true,
  co_xao_tron_dap_an: true,
  hien_thi_ket_qua_ngay_lap: true,
  hien_thi_dap_an_dung: true,
  cho_xem_lai_test: true,
  trang_thai: 1,
});

const lessons = ref([]);
const selectedLesson = ref(null);

const questions = ref([]);
const showQuestionEditor = ref(false);
const selectedQuestion = ref(null);
const selectedQuestionType = ref("multiple_choice");
const usingTemplate = ref(false);
const templateName = ref("");
const formErrors = ref({});

// Computed
const loading = computed(() => testStore.loading);
const isEdit = computed(() => !!route.params.id);
const editTestId = computed(() => route.params.id);

const progressPercent = computed(() => {
  let completeness = 0;

  // Test name (20%)
  if (form.value.ten_bai_test) completeness += 20;

  // Test settings (30%)
  if (form.value.thoi_gian_toi_da && form.value.diem_tong_max) completeness += 30;

  // Questions added (50%)
  if (questions.value.length > 0) completeness += 50;

  return completeness;
});

// Methods
const truncate = (text, length) => {
  if (!text) return "";
  return text.length > length ? text.substring(0, length) + "..." : text;
};

const getQuizTypeDescription = (quizType) => {
  const descriptions = {
    listening: "Quiz nghe - tập trung vào kỹ năng nghe hiểu",
    writing: "Quiz viết - tập trung vào kỹ năng viết và biểu đạt",
    reading: "Quiz đọc - tập trung vào kỹ năng đọc hiểu",
    mixed: "Quiz hỗn hợp - kiểm tra năng lực toàn diện tất cả các kỹ năng"
  };
  return descriptions[quizType] || "";
};

const getQuizTypeLabel = (quizType) => {
  const labels = {
    listening: "🎧 Listening (Nghe)",
    writing: "✍️ Writing (Viết)",
    reading: "📖 Reading (Đọc)",
    mixed: "🎯 Mixed (Hỗn Hợp)"
  };
  return labels[quizType] || "Unknown";
};

const openQuestionEditor = (question = null) => {
  if (question) {
    selectedQuestion.value = { ...question };
    selectedQuestionType.value = question.loai_cau_hoi;
  } else {
    selectedQuestion.value = null;
    selectedQuestionType.value = "multiple_choice";
  }
  showQuestionEditor.value = true;
};

const onQuestionSave = (question) => {
  if (selectedQuestion.value && selectedQuestion.value.id) {
    // Update existing
    const index = questions.value.findIndex(q => q.id === question.id);
    if (index !== -1) {
      questions.value[index] = question;
    }
  } else {
    // Add new
    question.id = Date.now(); // Temporary ID for new questions
    questions.value.push(question);
  }
  showQuestionEditor.value = false;
};

const deleteQuestion = (index) => {
  if (confirm("Xóa câu hỏi này?")) {
    questions.value.splice(index, 1);
  }
};

const onSubmit = async () => {
  // Validate form
  formErrors.value = {};

  if (!form.value.id_lesson) {
    formErrors.value.id_lesson = "Bài học là bắt buộc";
  }

  if (!form.value.ten_bai_test || form.value.ten_bai_test.trim().length === 0) {
    formErrors.value.ten_bai_test = "Tên bài test không được để trống";
  }

  if (!form.value.thoi_gian_toi_da || form.value.thoi_gian_toi_da < 1) {
    formErrors.value.thoi_gian_toi_da = "Thời gian tối thiểu phải là 1 phút";
  }

  if (!form.value.diem_tong_max || form.value.diem_tong_max < 1) {
    formErrors.value.diem_tong_max = "Điểm tối đa phải lớn hơn 0";
  }

  if (questions.value.length === 0) {
    alert("Vui lòng thêm ít nhất một câu hỏi");
    return;
  }

  // If there are errors, return
  if (Object.keys(formErrors.value).length > 0) {
    alert("Vui lòng kiểm tra lại biểu mẫu");
    return;
  }

  try {
    const formData = new FormData();

    // Add form fields
    formData.append('id_lesson', form.value.id_lesson);
    formData.append('ten_bai_test', form.value.ten_bai_test);
    formData.append('loai_quiz', form.value.loai_quiz);
    formData.append('chi_tiet_loai_quiz', form.value.chi_tiet_loai_quiz || '');
    formData.append('mo_ta', form.value.mo_ta);
    formData.append('thoi_gian_toi_da', form.value.thoi_gian_toi_da);
    formData.append('diem_tong_max', form.value.diem_tong_max);
    formData.append('so_lan_lam_toi_da', form.value.so_lan_lam_toi_da);
    formData.append('diem_dat', form.value.diem_dat);
    formData.append('co_xao_tron_cau_hoi', form.value.co_xao_tron_cau_hoi ? 1 : 0);
    formData.append('co_xao_tron_dap_an', form.value.co_xao_tron_dap_an ? 1 : 0);
    formData.append('hien_thi_ket_qua_ngay_lap', form.value.hien_thi_ket_qua_ngay_lap ? 1 : 0);
    formData.append('hien_thi_dap_an_dung', form.value.hien_thi_dap_an_dung ? 1 : 0);
    formData.append('cho_xem_lai_test', form.value.cho_xem_lai_test ? 1 : 0);
    formData.append('trang_thai', form.value.trang_thai);

    // Add questions with audio file handling
    const questionsForSubmit = questions.value.map((q, index) => {
      // Extract audio file and audio remove flag if present
      const { _audioFile, _removeAudio, ...questionData } = q;

      // Append audio file to FormData if present
      if (_audioFile) {
        formData.append(`audio_${index}`, _audioFile);
      }

      // Store remove audio flag if needed
      if (_removeAudio) {
        questionData._removeAudio = true;
      }

      return questionData;
    });

    formData.append('questions', JSON.stringify(questionsForSubmit));

    if (isEdit.value) {
      await testStore.updateTest(editTestId.value, formData);
      alert("Cập nhật bài test thành công!");
    } else {
      await testStore.createTest(formData);
      alert("Tạo bài test thành công!");
    }

    router.push("/tests");
  } catch (err) {
    alert("Lỗi: " + testStore.error);
  }
};

const onBack = () => {
  if (confirm("Bạn có chắc chắn muốn quay lại? Các thay đổi sẽ không được lưu.")) {
    router.back();
  }
};

// Load lessons
const loadLessons = async () => {
  try {
    const http = await import("@/api/axiosClient").then(m => m.default);
    const response = await http.get('/lessons');
    // Response structure: { success: true, data: [...lessons] }
    if (response && Array.isArray(response.data)) {
      lessons.value = response.data;
    } else {
      lessons.value = [];
    }
  } catch (err) {
    console.error('Lỗi khi tải danh sách bài học:', err);
    lessons.value = [];
  }
};

// Load test if editing
const loadTest = async () => {
  // Load template preset if creating from template
  if (!isEdit.value) {
    const preset = sessionStorage.getItem('testTemplatePreset');
    const presetName = sessionStorage.getItem('testTemplateName');
    if (preset) {
      try {
        const presetData = JSON.parse(preset);
        // Normalize boolean values from preset
        form.value = {
          ...form.value,
          ...presetData,
          co_xao_tron_cau_hoi: !!presetData.co_xao_tron_cau_hoi,
          co_xao_tron_dap_an: !!presetData.co_xao_tron_dap_an,
          hien_thi_ket_qua_ngay_lap: !!presetData.hien_thi_ket_qua_ngay_lap,
          hien_thi_dap_an_dung: !!presetData.hien_thi_dap_an_dung,
          cho_xem_lai_test: !!presetData.cho_xem_lai_test,
        };
        usingTemplate.value = true;
        templateName.value = presetName || 'Custom';
        // Show template indicator for 3 seconds
        setTimeout(() => {
          usingTemplate.value = false;
        }, 3000);
        sessionStorage.removeItem('testTemplatePreset');
        sessionStorage.removeItem('testTemplateName');
      } catch (e) {
        console.error('Error loading template preset', e);
      }
    }
  }

  // Load test if editing
  if (isEdit.value) {
    try {
      const test = await testStore.fetchTestDetail(editTestId.value);

      // Normalize boolean values from database (may be 0/1 or true/false)
      form.value = {
        ...test,
        co_xao_tron_cau_hoi: !!test.co_xao_tron_cau_hoi,
        co_xao_tron_dap_an: !!test.co_xao_tron_dap_an,
        hien_thi_ket_qua_ngay_lap: !!test.hien_thi_ket_qua_ngay_lap,
        hien_thi_dap_an_dung: !!test.hien_thi_dap_an_dung,
        cho_xem_lai_test: !!test.cho_xem_lai_test,
      };
      questions.value = test.questions || [];
    } catch (err) {
      alert("Lỗi khi tải bài test: " + testStore.error);
      router.back();
    }
  }
};

// Watchers
watch(() => form.value.id_lesson, (newLessonId) => {
  if (newLessonId) {
    const lesson = lessons.value.find(l => l.id === newLessonId);
    selectedLesson.value = lesson || null;
  } else {
    selectedLesson.value = null;
  }
});

// Lifecycle
onMounted(() => {
  loadLessons();
  loadTest();
});
</script>

<style scoped>
.sticky-top {
  z-index: 10;
}

.list-group-item {
  border-left: 4px solid #007bff;
}

.btn-group-sm .btn {
  font-size: 0.75rem;
}
</style>
