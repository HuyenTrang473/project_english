<template>
  <div class="lesson-container">
    <div class="lesson-header">
      <h1><i class="fa fa-book"></i> Quản Lý Bài Học</h1>
      <div class="header-buttons">
        <router-link to="/admin" class="btn-back">← Quay Lại</router-link>
        <router-link v-if="isTeacher" to="/lessons/create" class="btn btn-primary">
          <i class="fa fa-plus-circle"></i> Tạo Bài Học Mới
        </router-link>
      </div>
    </div>

    <!-- Search & Filter -->
    <div class="search-section">
      <input v-model="searchQuery" type="text" placeholder="Tìm kiếm bài học..." class="search-input" />
      <select v-if="isTeacher" v-model="selectedStatus" class="status-select">
        <option value="">Tất cả trạng thái</option>
        <option value="1">Nháp</option>
        <option value="2">Đã xuất bản</option>
      </select>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading">Đang tải...</div>

    <!-- Error State -->
    <div v-if="error" class="error-message">
      <i class="fa fa-exclamation-triangle"></i> {{ error }}
    </div>

    <!-- Lessons Grid -->
    <div v-if="!loading && lessons.length > 0" class="lessons-grid">
      <div v-for="lesson in filteredLessons" :key="lesson.id" class="lesson-card">
        <div class="lesson-card-header">
          <h3>{{ lesson.title }}</h3>
          <span v-if="lesson.statusText" :class="`status-badge status-${lesson.status}`">
            {{ lesson.statusText }}
          </span>
        </div>

        <p class="lesson-description">{{ lesson.description }}</p>

        <div class="lesson-meta">
          <small><i class="fa fa-user"></i> {{ lesson.teacher?.name || 'Unknown' }}</small>
          <small><i class="fa fa-calendar"></i> {{ formatDate(lesson.createdAt) }}</small>
        </div>

        <div class="lesson-actions">
          <router-link :to="`/lessons/${lesson.id}`" class="btn btn-sm btn-view">
            <i class="fa fa-eye"></i> Xem
          </router-link>

          <router-link v-if="isTeacher && isOwner(lesson)" :to="`/lessons/${lesson.id}/edit`"
            class="btn btn-sm btn-edit">
            <i class="fa fa-pencil"></i> Sửa
          </router-link>

          <button v-if="isTeacher && isOwner(lesson)" @click="toggleStatus(lesson.id)"
            :class="`btn btn-sm btn-${lesson.status === 2 ? 'unpublish' : 'publish'}`">
            <i :class="lesson.status === 2 ? 'fa fa-lock' : 'fa fa-unlock'"></i>
            {{ lesson.status === 2 ? ' Giấu' : ' Xuất bản' }}
          </button>

          <button v-if="isTeacher && isOwner(lesson)" @click="deleteLesson(lesson.id)" class="btn btn-sm btn-delete">
            <i class="fa fa-trash"></i> Xóa
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!loading && lessons.length === 0" class="empty-state">
      <p><i class="fa fa-inbox"></i> Không có bài học nào</p>
    </div>
  </div>
</template>

<script>
import * as lessonApi from "@/api/lessonApi";
import { useAuthStore } from "@/stores/auth";

export default {
  name: "LessonList",
  data() {
    return {
      lessons: [],
      loading: false,
      error: null,
      searchQuery: "",
      selectedStatus: "",
      authStore: useAuthStore(),
    };
  },
  computed: {
    isTeacher() {
      return this.authStore.user?.role === "giao_vien" || this.authStore.isAdmin;
    },
    filteredLessons() {
      return this.lessons.filter((lesson) => {
        // Filter by search query
        if (
          this.searchQuery &&
          !lesson.title.toLowerCase().includes(this.searchQuery.toLowerCase()) &&
          !lesson.description?.toLowerCase().includes(this.searchQuery.toLowerCase())
        ) {
          return false;
        }

        // Filter by status (only for teacher)
        if (this.selectedStatus && lesson.status != this.selectedStatus) {
          return false;
        }

        return true;
      });
    },
  },
  methods: {
    async loadLessons() {
      this.loading = true;
      this.error = null;
      try {
        let response;
        console.log('Loading lessons... isTeacher:', this.isTeacher, 'authenticated:', this.authStore.isAuthenticated);

        if (this.isTeacher) {
          // Teachers see all their lessons (draft + published)
          try {
            response = await lessonApi.getMyLessons();
          } catch (err) {
            console.error("Error loading teacher lessons:", err);
            // Fallback to public lessons if teacher endpoint fails
            this.error = "Lỗi khi tải bài học của bạn. Cố gắng tải bài công khai...";
            response = await lessonApi.getLessons();
          }
        } else {
          // Students see only published lessons
          response = await lessonApi.getLessons();
        }

        console.log('Response:', response);

        if (response) {
          if (response.success) {
            this.lessons = response.data || [];
            this.error = null;
          } else {
            this.error = response.message || "Lỗi khi tải bài học";
            this.lessons = [];
          }
        } else {
          this.error = "Không nhận được phản hồi từ server";
          this.lessons = [];
        }
      } catch (err) {
        console.error("Error loading lessons:", err?.response?.data || err.message || err);
        this.error = err?.response?.data?.message || err.message || "Lỗi khi tải bài học";
        this.lessons = [];
      } finally {
        this.loading = false;
      }
    },
    isOwner(lesson) {
      return lesson.teacher?.id === this.authStore.user?.id || this.authStore.isAdmin;
    },
    async toggleStatus(lessonId) {
      if (!confirm("Thay đổi trạng thái bài học?")) return;

      try {
        const response = await lessonApi.toggleLessonStatus(lessonId);
        if (response && response.success) {
          // Reload
          this.loadLessons();
          this.showSuccessMessage(response.message);
        }
      } catch (err) {
        this.error = "Lỗi khi thay đổi trạng thái";
        console.error(err);
      }
    },
    async deleteLesson(lessonId) {
      if (!confirm("Bạn chắc chắn muốn xóa bài học này?")) return;

      try {
        const response = await lessonApi.deleteLesson(lessonId);
        if (response && response.success) {
          this.loadLessons();
          this.showSuccessMessage("Xóa bài học thành công!");
        }
      } catch (err) {
        this.error = "Lỗi khi xóa bài học";
        console.error(err);
      }
    },
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString("vi-VN");
    },
    showSuccessMessage(message) {
      // You can use your notification library here
      alert(message);
    },
  },
  watch: {
    'authStore.isAuthenticated'(newVal) {
      console.log('Authentication changed:', newVal);
      if (newVal) {
        this.loadLessons();
      }
    },
  },
  mounted() {
    console.log('LessonList mounted, authStore:', this.authStore);
    this.loadLessons();
  },
};
</script>

<style scoped>
.lesson-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.lesson-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  border-bottom: 2px solid #ddd;
  padding-bottom: 15px;
}

.lesson-header h1 {
  margin: 0;
  color: #333;
}

.header-buttons {
  display: flex;
  gap: 10px;
  align-items: center;
}

.btn-back {
  padding: 8px 12px;
  color: #666;
  text-decoration: none;
  border-radius: 4px;
  font-size: 14px;
  display: inline-block;
  transition: background 0.2s;
}

.btn-back:hover {
  background: #f0f0f0;
}

.btn-secondary {
  background: #6c757d;
  color: white;
  padding: 10px 16px;
  text-decoration: none;
  border-radius: 4px;
  font-size: 14px;
  display: inline-block;
  transition: background 0.2s;
}

.btn-secondary:hover {
  background: #5a6268;
}

.search-section {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

.search-input {
  flex: 1;
  padding: 10px 15px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.status-select {
  padding: 10px 15px;
  border: 1px solid #ddd;
  border-radius: 4px;
  background: white;
  cursor: pointer;
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

.lessons-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

.lesson-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 15px;
  background: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s, box-shadow 0.2s;
}

.lesson-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.lesson-card-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: 10px;
}

.lesson-card-header h3 {
  margin: 0;
  flex: 1;
  color: #333;
  font-size: 16px;
}

.status-badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: bold;
  white-space: nowrap;
}

.status-1 {
  background: #fff3cd;
  color: #856404;
}

.status-2 {
  background: #d4edda;
  color: #155724;
}

.lesson-description {
  color: #666;
  font-size: 13px;
  margin: 10px 0;
  line-height: 1.5;
}

.lesson-meta {
  display: flex;
  justify-content: space-between;
  color: #999;
  font-size: 12px;
  margin: 10px 0;
}

.lesson-actions {
  display: flex;
  gap: 5px;
  margin-top: 10px;
  flex-wrap: wrap;
}

.btn {
  padding: 6px 10px;
  border: none;
  border-radius: 4px;
  font-size: 12px;
  cursor: pointer;
  text-decoration: none;
  display: inline-block;
  transition: background 0.2s;
}

.btn-small {
  font-size: 11px;
  padding: 4px 8px;
}

.btn-primary {
  background: #007bff;
  color: white;
}

.btn-primary:hover {
  background: #0056b3;
}

.btn-sm {
  font-size: 11px;
  padding: 4px 8px;
  flex: 1;
  min-width: 60px;
}

.btn-view {
  background: #17a2b8;
  color: white;
}

.btn-view:hover {
  background: #138496;
}

.btn-edit {
  background: #ffc107;
  color: black;
}

.btn-edit:hover {
  background: #e0a800;
}

.btn-publish {
  background: #28a745;
  color: white;
}

.btn-publish:hover {
  background: #218838;
}

.btn-unpublish {
  background: #6c757d;
  color: white;
}

.btn-unpublish:hover {
  background: #5a6268;
}

.btn-delete {
  background: #dc3545;
  color: white;
}

.btn-delete:hover {
  background: #c82333;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #999;
  font-size: 18px;
}
</style>
