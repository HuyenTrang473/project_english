<template>
  <div class="teacher-dashboard">
    <div class="dashboard-header">
      <h1>Quản Lý Bài Thi</h1>
    </div>

    <!-- Tab Navigation -->
    <div class="tabs">
      <button class="tab-button" :class="{ active: activeTab === 'list' }" @click="activeTab = 'list'">
        Danh Sách Bài Thi
      </button>
      <button class="tab-button" :class="{ active: activeTab === 'create' }" @click="activeTab = 'create'">
        Tạo Bài Thi Mới
      </button>
    </div>

    <!-- Tab: Danh Sách Bài Thi -->
    <div v-if="activeTab === 'list'" class="tab-content">
      <div v-if="loading" class="loading">Đang tải...</div>
      <div v-else-if="tests.length === 0" class="no-data">
        Chưa có bài thi nào. Bắt đầu bằng cách tạo bài thi mới!
      </div>
      <div v-else class="tests-list">
        <div v-for="test in tests" :key="test.id" class="test-card" @click="editingTest = test; activeTab = 'create'">
          <div class="test-card-header">
            <h3>{{ test.ten_bai_test }}</h3>
            <span :class="['status', `status-${test.trang_thai}`]">
              {{ getStatusLabel(test.trang_thai) }}
            </span>
          </div>
          <p class="test-description">{{ test.mo_ta }}</p>
          <div class="test-meta">
            <span><strong>Loại:</strong> {{ getTestTypeLabel(test.loai_bai_test) }}</span>
            <span><strong>Thời gian:</strong> {{ test.thoi_gian_toi_da }} phút</span>
            <span><strong>Điểm tối đa:</strong> {{ test.diem_tong_max }}</span>
          </div>
          <div class="test-actions">
            <button class="btn-secondary" @click.stop="viewQuestions(test)">
              Xem/Sửa Câu Hỏi ({{ test.questions_count || 0 }})
            </button>
            <button class="btn-danger" @click.stop="deleteTest(test.id)" :disabled="deleting">
              Xóa
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab: Tạo/Sửa Bài Thi -->
    <div v-if="activeTab === 'create'" class="tab-content">
      <div v-if="editingTest" class="question-editor">
        <h2>Quản Lý Câu Hỏi: {{ editingTest.ten_bai_test }}</h2>
        <button class="btn-back" @click="closeQuestionEditor">← Quay Lại</button>

        <!-- Danh Sách Câu Hỏi -->
        <div class="questions-section">
          <h3>Câu Hỏi ({{ editingTest.cau_hois?.length || 0 }})</h3>
          <div v-if="!loadingQuestions" class="questions-list">
            <div v-for="(question, index) in editingTest.cau_hois" :key="question.id" class="question-item">
              <div class="question-header">
                <span class="question-number">Câu {{ index + 1 }}</span>
                <span :class="['question-type', `type-${question.type}`]">
                  {{ getQuestionTypeLabel(question.type) }}
                </span>
                <span class="question-score">{{ question.maxScore }} điểm</span>
              </div>
              <p class="question-content">{{ question.content }}</p>

              <!-- Danh Sách Đáp Án -->
              <div class="answers-list">
                <div v-for="answer in question.answers" :key="answer.id" class="answer-item"
                  :class="{ correct: answer.isCorrect }">
                  <span class="answer-content">{{ answer.content }}</span>
                  <span v-if="answer.isCorrect" class="correct-badge"><i class="fa fa-check"></i> Đúng</span>
                  <button class="btn-edit-small" @click="editAnswer(question, answer)">
                    Sửa
                  </button>
                  <button class="btn-delete-small" @click="deleteAnswer(editingTest.id, question.id, answer.id)">
                    Xóa
                  </button>
                </div>
              </div>

              <!-- Thêm Đáp Án -->
              <form @submit.prevent="saveAnswer(question)" class="add-answer-form">
                <input v-model="newAnswer.content" type="text" placeholder="Thêm đáp án..." required />
                <label class="checkbox">
                  <input v-model="newAnswer.isCorrect" type="checkbox" />
                  Đáp án đúng
                </label>
                <button type="submit" class="btn-primary-small">Thêm</button>
              </form>

              <div class="question-actions">
                <button class="btn-edit" @click="editQuestion(question)">
                  Sửa Câu Hỏi
                </button>
                <button class="btn-danger" @click="deleteQuestion(editingTest.id, question.id)">
                  Xóa Câu
                </button>
              </div>
            </div>
          </div>

          <!-- Thêm Câu Hỏi Mới -->
          <form @submit.prevent="saveQuestion" class="add-question-form">
            <h4>Thêm Câu Hỏi Mới</h4>
            <div class="form-group">
              <label>Nội dung câu hỏi</label>
              <textarea v-model="newQuestion.content" placeholder="Nhập nội dung câu hỏi..." required></textarea>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Loại câu hỏi</label>
                <select v-model.number="newQuestion.type" required>
                  <option value="1">Trắc nghiệm một đáp án</option>
                  <option value="2">Trắc nghiệm nhiều đáp án</option>
                  <option value="3">Tự luận</option>
                </select>
              </div>
              <div class="form-group">
                <label>Điểm tối đa</label>
                <input v-model.number="newQuestion.maxScore" type="number" min="0.5" step="0.5" required />
              </div>
            </div>
            <button type="submit" class="btn-primary">Thêm Câu Hỏi</button>
          </form>
        </div>
      </div>

      <div v-else class="test-form">
        <h2>{{ formMode === 'create' ? 'Tạo Bài Thi Mới' : 'Sửa Bài Thi' }}</h2>
        <form @submit.prevent="saveTest">
          <div class="form-group">
            <label>Tên bài thi</label>
            <input v-model="formData.ten_bai_test" type="text" placeholder="VD: Unit 1 Midterm Test" required />
          </div>

          <div class="form-group">
            <label>Mô tả</label>
            <textarea v-model="formData.mo_ta" placeholder="Mô tả chi tiết về bài thi..." required></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Bài học</label>
              <select v-model.number="formData.id_lesson" required>
                <option value="" disabled>-- Chọn bài học --</option>
                <option v-for="lesson in lessons" :key="lesson.id" :value="lesson.id">
                  {{ lesson.ten_bai_hoc }}
                </option>
              </select>
            </div>

            <div class="form-group">
              <label>Loại bài thi</label>
              <select v-model.number="formData.loai_bai_test" required>
                <option value="1">Quiz</option>
                <option value="2">Midterm</option>
                <option value="3">Final</option>
                <option value="4">Practice</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Thời gian làm bài (phút)</label>
              <input v-model.number="formData.thoi_gian_toi_da" type="number" min="1" required />
            </div>

            <div class="form-group">
              <label>Điểm tối đa</label>
              <input v-model.number="formData.diem_tong_max" type="number" min="1" step="0.5" required />
            </div>
          </div>

          <div class="form-group checkbox">
            <label>
              <input v-model.number="formData.trang_thai" type="checkbox" :true-value="2" :false-value="1" />
              Công khai bài thi
            </label>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn-primary" :disabled="submitting">
              {{ formMode === 'create' ? 'Tạo Bài Thi' : 'Cập Nhật' }}
            </button>
            <button type="button" class="btn-secondary" @click="resetForm">
              Hủy
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from "@/stores/auth";
import {
  getMyTests,
  getQuestionsByTest,
  createTest,
  updateTest,
  deleteTest,
  createQuestion,
  updateQuestion,
  deleteQuestion,
  createAnswer,
  updateAnswer,
  deleteAnswer,
} from "@/api/testApi";
import { getLessons } from "@/api/lessonApi";

export default {
  name: "TeacherDashboard",
  data() {
    return {
      tests: [],
      lessons: [],
      loading: false,
      submitting: false,
      deleting: false,
      loadingQuestions: false,
      activeTab: "list",
      formMode: "create",
      editingTest: null,
      editingQuestion: null,
      editingAnswer: null,
      newQuestion: {
        content: "",
        type: 1,
        maxScore: 1,
      },
      newAnswer: {
        content: "",
        isCorrect: false,
      },
      formData: {
        ten_bai_test: "",
        mo_ta: "",
        id_lesson: "",
        loai_bai_test: 1,
        thoi_gian_toi_da: 45,
        diem_tong_max: 100,
        trang_thai: 1,
      },
    };
  },

  computed: {
    authStore() {
      return useAuthStore();
    },
  },

  async mounted() {
    await this.loadTests();
    await this.loadLessons();
  },

  methods: {
    async loadTests() {
      this.loading = true;
      try {
        const { data } = await getMyTests();
        this.tests = data.data || [];
      } catch (error) {
        console.error("Lỗi khi tải danh sách bài thi:", error);
        this.$notify?.("error", "Lỗi khi tải danh sách bài thi");
      } finally {
        this.loading = false;
      }
    },

    async loadLessons() {
      try {
        const { data } = await getLessons();
        this.lessons = data.data || [];
      } catch (error) {
        console.error("Lỗi khi tải danh sách bài học:", error);
      }
    },

    async saveTest() {
      this.submitting = true;
      try {
        if (this.formMode === "create") {
          await createTest(this.formData);
          this.$notify?.("success", "Tạo bài thi thành công!");
        } else {
          await updateTest(this.editingTest.id, this.formData);
          this.$notify?.("success", "Cập nhật bài thi thành công!");
        }
        this.resetForm();
        await this.loadTests();
        this.activeTab = "list";
      } catch (error) {
        console.error("Lỗi khi lưu bài thi:", error);
        this.$notify?.("error", error.response?.data?.message || "Lỗi khi lưu bài thi");
      } finally {
        this.submitting = false;
      }
    },

    async deleteTest(testId) {
      if (!confirm("Bạn chắc chắn muốn xóa bài thi này?")) return;
      this.deleting = true;
      try {
        await deleteTest(testId);
        this.$notify?.("success", "Xóa bài thi thành công!");
        await this.loadTests();
      } catch (error) {
        console.error("Lỗi khi xóa bài thi:", error);
        this.$notify?.("error", "Lỗi khi xóa bài thi");
      } finally {
        this.deleting = false;
      }
    },

    async viewQuestions(test) {
      this.editingTest = { ...test };
      this.loadingQuestions = true;
      try {
        const { data } = await getQuestionsByTest(test.id);
        this.editingTest.cau_hois = data.data || [];
      } catch (error) {
        console.error("Lỗi khi tải câu hỏi:", error);
        this.$notify?.("error", "Lỗi khi tải câu hỏi");
      } finally {
        this.loadingQuestions = false;
      }
    },

    async saveQuestion() {
      try {
        if (this.newQuestion.content.trim() === "") {
          this.$notify?.("error", "Vui lòng nhập nội dung câu hỏi");
          return;
        }

        await createQuestion(this.editingTest.id, {
          content: this.newQuestion.content,
          type: this.newQuestion.type,
          maxScore: this.newQuestion.maxScore,
        });
        this.$notify?.("success", "Thêm câu hỏi thành công!");
        this.newQuestion = { content: "", type: 1, maxScore: 1 };
        await this.viewQuestions(this.editingTest);
      } catch (error) {
        console.error("Lỗi khi thêm câu hỏi:", error);
        this.$notify?.("error", error.response?.data?.message || "Lỗi khi thêm câu hỏi");
      }
    },

    async deleteQuestion(testId, questionId) {
      if (!confirm("Xóa câu hỏi này?")) return;
      try {
        await deleteQuestion(testId, questionId);
        this.$notify?.("success", "Xóa câu hỏi thành công!");
        await this.viewQuestions(this.editingTest);
      } catch (error) {
        console.error("Lỗi khi xóa câu hỏi:", error);
        this.$notify?.("error", "Lỗi khi xóa câu hỏi");
      }
    },

    async saveAnswer(question) {
      try {
        if (this.newAnswer.content.trim() === "") {
          this.$notify?.("error", "Vui lòng nhập nội dung đáp án");
          return;
        }

        await createAnswer(this.editingTest.id, question.id, {
          content: this.newAnswer.content,
          isCorrect: this.newAnswer.isCorrect,
        });
        this.$notify?.("success", "Thêm đáp án thành công!");
        this.newAnswer = { content: "", isCorrect: false };
        await this.viewQuestions(this.editingTest);
      } catch (error) {
        console.error("Lỗi khi thêm đáp án:", error);
        this.$notify?.("error", error.response?.data?.message || "Lỗi khi thêm đáp án");
      }
    },

    async deleteAnswer(testId, questionId, answerId) {
      if (!confirm("Xóa đáp án này?")) return;
      try {
        await deleteAnswer(testId, questionId, answerId);
        this.$notify?.("success", "Xóa đáp án thành công!");
        await this.viewQuestions(this.editingTest);
      } catch (error) {
        console.error("Lỗi khi xóa đáp án:", error);
        this.$notify?.("error", "Lỗi khi xóa đáp án");
      }
    },

    editQuestion(question) {
      this.editingQuestion = question;
      this.newQuestion = { ...question };
    },

    editAnswer(question, answer) {
      this.editingAnswer = answer;
      this.newAnswer = { ...answer };
    },

    closeQuestionEditor() {
      this.editingTest = null;
      this.editingQuestion = null;
      this.editingAnswer = null;
      this.newQuestion = { content: "", type: 1, maxScore: 1 };
      this.newAnswer = { content: "", isCorrect: false };
      this.activeTab = "list";
    },

    resetForm() {
      this.formMode = "create";
      this.editingTest = null;
      this.formData = {
        ten_bai_test: "",
        mo_ta: "",
        id_lesson: "",
        loai_bai_test: 1,
        thoi_gian_toi_da: 45,
        diem_tong_max: 100,
        trang_thai: 1,
      };
    },

    getStatusLabel(status) {
      const labels = { 1: "Nháp", 2: "Công khai" };
      return labels[status] || "Không xác định";
    },

    getTestTypeLabel(type) {
      const labels = { 1: "Quiz", 2: "Midterm", 3: "Final", 4: "Practice" };
      return labels[type] || "Không xác định";
    },

    getQuestionTypeLabel(type) {
      const labels = { 1: "1 Đáp án", 2: "Nhiều đáp án", 3: "Tự luận" };
      return labels[type] || "Không xác định";
    },
  },
};
</script>

<style scoped>
.teacher-dashboard {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  background: #f5f5f5;
  border-radius: 8px;
}

.dashboard-header {
  margin-bottom: 30px;
}

.dashboard-header h1 {
  font-size: 28px;
  margin: 0;
  color: #333;
}

/* Tabs */
.tabs {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
  border-bottom: 2px solid #ddd;
}

.tab-button {
  padding: 10px 20px;
  border: none;
  background: transparent;
  cursor: pointer;
  font-size: 16px;
  color: #666;
  transition: all 0.3s;
  border-bottom: 3px solid transparent;
}

.tab-button.active {
  color: #0066cc;
  border-bottom-color: #0066cc;
}

.tab-button:hover {
  color: #0066cc;
}

/* Tab Content */
.tab-content {
  background: white;
  padding: 20px;
  border-radius: 8px;
}

.loading,
.no-data {
  text-align: center;
  padding: 40px;
  color: #999;
  font-size: 16px;
}

/* Tests List */
.tests-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 20px;
}

.test-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 15px;
  background: white;
  cursor: pointer;
  transition: all 0.3s;
}

.test-card:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.test-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.test-card-header h3 {
  margin: 0;
  font-size: 18px;
  color: #333;
}

.status {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: bold;
}

.status-0 {
  background: #f0f0f0;
  color: #999;
}

.status-1 {
  background: #e3f2fd;
  color: #1976d2;
}

.status-2 {
  background: #ffebee;
  color: #c62828;
}

.test-description {
  margin: 10px 0;
  color: #666;
  font-size: 14px;
  line-height: 1.4;
}

.test-meta {
  display: flex;
  flex-direction: column;
  gap: 5px;
  font-size: 13px;
  color: #888;
  margin: 10px 0;
}

.test-actions {
  display: flex;
  gap: 10px;
  margin-top: 15px;
}

/* Question Editor */
.question-editor {
  max-width: 100%;
}

.question-editor h2 {
  margin-top: 0;
  color: #333;
}

.btn-back {
  padding: 8px 16px;
  background: #f0f0f0;
  border: 1px solid #ddd;
  border-radius: 4px;
  cursor: pointer;
  margin-bottom: 20px;
  transition: all 0.3s;
}

.btn-back:hover {
  background: #e0e0e0;
}

/* Questions Section */
.questions-section {
  margin-bottom: 30px;
}

.questions-section h3 {
  margin-top: 0;
  color: #333;
}

.questions-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
  margin-bottom: 20px;
}

.question-item {
  border: 1px solid #e0e0e0;
  border-radius: 6px;
  padding: 15px;
  background: #fafafa;
}

.question-header {
  display: flex;
  gap: 10px;
  align-items: center;
  margin-bottom: 10px;
  flex-wrap: wrap;
}

.question-number {
  font-weight: bold;
  color: #0066cc;
}

.question-type {
  padding: 2px 6px;
  border-radius: 3px;
  font-size: 12px;
  background: #e3f2fd;
  color: #1976d2;
}

.question-score {
  margin-left: auto;
  color: #888;
  font-size: 14px;
}

.question-content {
  margin: 10px 0;
  color: #333;
  line-height: 1.5;
}

/* Answers List */
.answers-list {
  margin: 10px 0;
  padding-left: 20px;
  border-left: 2px solid #ddd;
}

.answer-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px;
  margin: 5px 0;
  background: white;
  border-radius: 4px;
  border: 1px solid #eee;
}

.answer-item.correct {
  border-color: #4caf50;
  background: #f1f8f4;
}

.answer-content {
  flex: 1;
  color: #333;
}

.correct-badge {
  color: #4caf50;
  font-weight: bold;
  font-size: 12px;
}

/* Add Question Form */
.add-question-form,
.add-answer-form {
  background: #f9f9f9;
  border: 1px dashed #ddd;
  border-radius: 6px;
  padding: 15px;
  margin-top: 15px;
}

.add-question-form h4,
.add-answer-form h4 {
  margin-top: 0;
  color: #333;
}

.add-answer-form {
  display: flex;
  gap: 10px;
  align-items: center;
  flex-wrap: wrap;
}

.add-answer-form input[type="text"] {
  flex: 1;
  min-width: 150px;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.add-answer-form .checkbox {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 14px;
  color: #666;
}

/* Forms */
.test-form {
  max-width: 600px;
}

.test-form h2 {
  margin-top: 0;
  color: #333;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  color: #333;
  font-weight: 500;
}

.form-group input[type="text"],
.form-group input[type="number"],
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  font-family: inherit;
}

.form-group textarea {
  resize: vertical;
  min-height: 100px;
}

.form-group.checkbox {
  display: flex;
  align-items: center;
}

.form-group.checkbox label {
  display: flex;
  align-items: center;
  gap: 8px;
  margin: 0;
}

.form-group.checkbox input[type="checkbox"] {
  width: auto;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}

/* Buttons */
.btn-primary,
.btn-secondary,
.btn-danger,
.btn-edit,
.btn-delete-small,
.btn-edit-small,
.btn-primary-small {
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.3s;
  font-weight: 500;
}

.btn-primary,
.btn-primary-small {
  background: #0066cc;
  color: white;
}

.btn-primary:hover,
.btn-primary-small:hover {
  background: #0052a3;
}

.btn-secondary {
  background: #f0f0f0;
  color: #333;
  border: 1px solid #ddd;
}

.btn-secondary:hover {
  background: #e0e0e0;
}

.btn-edit,
.btn-edit-small {
  background: #4caf50;
  color: white;
}

.btn-edit:hover,
.btn-edit-small:hover {
  background: #388e3c;
}

.btn-danger,
.btn-delete-small {
  background: #f44336;
  color: white;
}

.btn-danger:hover,
.btn-delete-small:hover {
  background: #d32f2f;
}

.btn-primary-small,
.btn-delete-small,
.btn-edit-small {
  padding: 4px 8px;
  font-size: 12px;
}

.form-actions {
  display: flex;
  gap: 10px;
  margin-top: 20px;
}

.form-actions .btn-primary {
  flex: 1;
}

.question-actions {
  display: flex;
  gap: 10px;
  margin-top: 10px;
}

.question-actions button {
  flex: 1;
}

/* Responsive */
@media (max-width: 768px) {
  .tests-list {
    grid-template-columns: 1fr;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .test-meta {
    flex-direction: row;
    flex-wrap: wrap;
  }

  .question-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .question-score {
    margin-left: 0;
  }

  .add-answer-form {
    flex-direction: column;
  }

  .add-answer-form input[type="text"] {
    min-width: auto;
  }
}
</style>
