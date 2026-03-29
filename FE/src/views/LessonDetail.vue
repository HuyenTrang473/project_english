<template>
  <div class="lesson-detail-container">
    <router-link :to="backRoute" class="btn btn-back">← Quay Lại</router-link>

    <div v-if="loading" class="loading">Đang tải...</div>

    <div v-if="error" class="error-message">⚠️ {{ error }}</div>

    <div v-if="lesson && !loading" class="lesson-detail">
      <div class="lesson-detail-header">
        <div>
          <h1>{{ lesson.title }}</h1>
          <span v-if="lesson.statusText" :class="`status-badge status-${lesson.status}`">
            {{ lesson.statusText }}
          </span>
        </div>
        <div class="teacher-info">
          <strong>👨‍🏫 Giáo viên:</strong> {{ lesson.teacher?.name }}<br />
          <strong>📧 Email:</strong> {{ lesson.teacher?.email }}<br />
          <small>📅 {{ formatDate(lesson.createdAt) }}</small>
        </div>
      </div>

      <div class="lesson-content">
        <section v-if="lesson.description" class="section">
          <h2>📌 Mô Tả</h2>
          <p>{{ lesson.description }}</p>
        </section>

        <section v-if="lesson.file && lesson.file.url" class="section">
          <h2>📎 Tệp Đính Kèm</h2>
          <div class="file-section">
            <div class="file-info">
              <strong>📁 {{ getFileName(lesson.file.path) }}</strong>
              <span class="file-meta">{{ formatFileSize(lesson.file.size) }} • {{ lesson.file.type.toUpperCase()
              }}</span>
            </div>
            <a :href="lesson.file.url" download :title="`Tải ${getFileName(lesson.file.path)}`"
              class="btn btn-download">
              ⬇️ Tải File
            </a>
          </div>
        </section>

        <section class="section">
          <h2>📖 Nội Dung</h2>
          <div class="content-text">{{ lesson.content }}</div>
        </section>

        <section class="section">
          <h2>📝 Bài Kiểm Tra Liên Quan</h2>
          <div v-if="tests.length > 0" class="tests-list">
            <div v-for="test in tests" :key="test.id" class="test-item">
              <strong>📋 {{ test.ten_bai_test }}</strong>
              <p>{{ test.mo_ta }}</p>
              <router-link :to="`/test/${test.id}`" class="btn btn-sm btn-primary">
                ➤ Làm Bài
              </router-link>
            </div>
          </div>
          <div v-else class="empty-tests">
            <p>Chưa có bài kiểm tra nào cho bài học này.</p>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script>
import * as lessonApi from "@/api/lessonApi";

export default {
  name: "LessonDetail",
  data() {
    return {
      lesson: null,
      tests: [],
      loading: false,
      error: null,
    };
  },
  computed: {
    backRoute() {
      return this.$route.query.from === "home" ? "/" : "/lessons";
    },
  },
  methods: {
    async loadLesson(lessonId) {
      this.loading = true;
      this.error = null;

      try {
        const response = await lessonApi.getLessonDetail(lessonId);

        if (response && response.success) {
          this.lesson = response.data;
          // Load related tests
          this.loadTests(lessonId);
        } else {
          this.error = response?.message || "Không thể tải bài học";
        }
      } catch (err) {
        this.error = "Không thể tải bài học";
        console.error("Error loading lesson:", err);
      } finally {
        this.loading = false;
      }
    },
    async loadTests(lessonId) {
      try {
        // Import the test API function
        const { getListByLesson } = await import("@/api/testApi");
        const response = await getListByLesson(lessonId);
        this.tests = response.data || [];
      } catch (err) {
        console.error("Error loading tests:", err);
        this.tests = [];
      }
    },
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString("vi-VN");
    },
    getFileName(filePath) {
      if (!filePath) return "Unknown";
      return filePath.split("/").pop();
    },
    formatFileSize(bytes) {
      if (!bytes) return "0 B";
      const k = 1024;
      const sizes = ["B", "KB", "MB"];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + " " + sizes[i];
    },
  },
  mounted() {
    this.loadLesson(this.$route.params.id);
  },
};
</script>

<style scoped>
.lesson-detail-container {
  max-width: 900px;
  margin: 0 auto;
  padding: 20px;
}

.btn-back {
  display: inline-block;
  margin-bottom: 20px;
  padding: 8px 16px;
  background: #6c757d;
  color: white;
  border-radius: 4px;
  text-decoration: none;
  transition: background 0.2s;
}

.btn-back:hover {
  background: #5a6268;
}

.loading,
.error-message {
  text-align: center;
  padding: 30px;
  font-size: 16px;
}

.error-message {
  background: #fee;
  color: #c33;
  border-radius: 4px;
}

.lesson-detail {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.lesson-detail-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 30px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.lesson-detail-header h1 {
  margin: 0 0 15px 0;
  font-size: 28px;
}

.status-badge {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: bold;
}

.status-1 {
  background: rgba(255, 255, 255, 0.3);
  color: white;
}

.status-2 {
  background: rgba(76, 175, 80, 0.3);
  color: white;
}

.teacher-info {
  text-align: right;
  font-size: 13px;
}

.teacher-info strong {
  display: block;
}

.lesson-content {
  padding: 30px;
}

.section {
  margin-bottom: 30px;
}

.section h2 {
  color: #333;
  border-bottom: 2px solid #ddd;
  padding-bottom: 10px;
  margin-bottom: 15px;
  font-size: 18px;
}

.section p {
  color: #666;
  line-height: 1.6;
  white-space: pre-wrap;
  word-break: break-word;
}

.content-text {
  background: #f5f5f5;
  padding: 15px;
  border-radius: 4px;
  color: #333;
  line-height: 1.8;
  white-space: pre-wrap;
  word-break: break-word;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

.tests-list {
  display: grid;
  gap: 15px;
}

.test-item {
  border: 1px solid #ddd;
  padding: 15px;
  border-radius: 6px;
  background: #f9f9f9;
}

.test-item strong {
  display: block;
  color: #333;
  margin-bottom: 8px;
}

.test-item p {
  margin: 8px 0;
  color: #666;
  font-size: 13px;
}

.btn-sm {
  padding: 6px 12px;
  font-size: 12px;
  margin-top: 10px;
}

.btn-primary {
  background: #007bff;
  color: white;
  text-decoration: none;
  border-radius: 4px;
  display: inline-block;
  transition: background 0.2s;
}

.btn-primary:hover {
  background: #0056b3;
}

.empty-tests {
  text-align: center;
  padding: 20px;
  color: #999;
  font-size: 14px;
}

.file-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f0f7ff;
  border: 1px solid #b3d9ff;
  border-radius: 6px;
  padding: 15px;
  gap: 15px;
}

.file-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.file-info strong {
  color: #007bff;
  font-size: 14px;
  word-break: break-all;
  margin: 0;
}

.file-meta {
  color: #666;
  font-size: 12px;
}

.btn-download {
  background: #28a745;
  color: white;
  padding: 8px 16px;
  border-radius: 4px;
  text-decoration: none;
  font-weight: bold;
  white-space: nowrap;
  transition: background 0.2s;
  display: inline-block;
}

.btn-download:hover {
  background: #218838;
}
</style>
