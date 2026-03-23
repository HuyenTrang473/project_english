<template>
  <div class="lesson-editor-container">
    <div class="lesson-editor-header">
      <h1>{{ isEdit ? "✏️ Chỉnh Sửa Bài Học" : "➕ Tạo Bài Học Mới" }}</h1>
      <router-link to="/lessons" class="btn btn-secondary">← Quay Lại</router-link>
    </div>

    <form @submit.prevent="saveLesson" class="lesson-form">
      <!-- Title -->
      <div class="form-group">
        <label for="title">Tiêu Đề *</label>
        <input id="title" v-model="form.tieu_de" type="text" placeholder="Nhập tiêu đề bài học" maxlength="255"
          required />
        <small>{{ form.tieu_de?.length || 0 }}/255</small>
      </div>

      <!-- Description -->
      <div class="form-group">
        <label for="description">Mô Tả</label>
        <textarea id="description" v-model="form.mo_ta" placeholder="Nhập mô tả bài học (tối đa 1000 ký tự)"
          maxlength="1000" rows="3"></textarea>
        <small>{{ form.mo_ta?.length || 0 }}/1000</small>
      </div>

      <!-- Content -->
      <div class="form-group">
        <label for="content">Nội Dung *</label>
        <textarea id="content" v-model="form.noi_dung" placeholder="Nhập nội dung bài học" rows="8" required></textarea>
      </div>

      <!-- Status -->
      <div class="form-group">
        <label for="status">Trạng Thái *</label>
        <select id="status" v-model.number="form.trang_thai" required>
          <option value="1">📝 Nháp (Draft)</option>
          <option value="2">✅ Xuất Bản (Published)</option>
        </select>
      </div>

      <!-- File Upload -->
      <div class="form-group">
        <label for="file">📎 Tải File (PDF, DOC, DOCX, TXT - tối đa 10MB)</label>
        <input id="file" type="file" @change="handleFileChange" accept=".pdf,.doc,.docx,.txt" />
        <small v-if="form.file" class="file-info">
          ✓ File: {{ form.file.name }} ({{ formatFileSize(form.file.size) }})
        </small>
        <small v-if="existingFile" class="file-info" style="color: #666;">
          📁 File hiện tại: {{ existingFile.name }} ({{ formatFileSize(existingFile.size) }})
        </small>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="error-message">
        ⚠️ {{ error }}
      </div>

      <!-- Success Message -->
      <div v-if="success" class="success-message">
        ✅ {{ success }}
      </div>

      <!-- Form Actions -->
      <div class="form-actions">
        <button type="submit" class="btn btn-primary" :disabled="loading">
          {{ loading ? "Đang lưu..." : "💾 Lưu Bài Học" }}
        </button>
        <router-link to="/lessons" class="btn btn-secondary">❌ Hủy</router-link>
      </div>
    </form>
  </div>
</template>

<script>
import * as lessonApi from "@/api/lessonApi";
import { useAuthStore } from "@/stores/auth";

export default {
  name: "LessonEditor",
  data() {
    return {
      form: {
        tieu_de: "",
        mo_ta: "",
        noi_dung: "",
        trang_thai: 1, // draft by default
        file: null, // new file to upload
      },
      existingFile: null, // file info from existing lesson (edit mode)
      loading: false,
      error: null,
      success: null,
      isEdit: false,
      authStore: useAuthStore(),
    };
  },
  methods: {
    handleFileChange(event) {
      const file = event.target.files[0];
      if (file) {
        // Validate file size (10MB max = 10485760 bytes)
        const maxSize = 10 * 1024 * 1024;
        if (file.size > maxSize) {
          this.error = "File quá lớn! Tối đa 10MB";
          event.target.value = ""; // clear the input
          return;
        }
        // Validate file type
        const allowedTypes = [
          "application/pdf",
          "application/msword",
          "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
          "text/plain",
        ];
        if (!allowedTypes.includes(file.type)) {
          this.error = "Định dạng file không được hỗ trợ! (PDF, DOC, DOCX, TXT)";
          event.target.value = ""; // clear the input
          return;
        }
        this.form.file = file;
        this.error = null;
      }
    },
    formatFileSize(bytes) {
      if (!bytes) return "0 B";
      const k = 1024;
      const sizes = ["B", "KB", "MB"];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + " " + sizes[i];
    },
    async saveLesson() {
      this.loading = true;
      this.error = null;
      this.success = null;

      try {
        // Validate form data before sending
        if (!this.form.tieu_de || this.form.tieu_de.trim() === '') {
          this.error = "Tiêu đề không được để trống";
          this.loading = false;
          return;
        }
        if (!this.form.noi_dung || this.form.noi_dung.trim() === '') {
          this.error = "Nội dung không được để trống";
          this.loading = false;
          return;
        }
        if (!this.form.trang_thai) {
          this.error = "Trạng thái không được để trống";
          this.loading = false;
          return;
        }

        // Prepare form data
        const formData = new FormData();

        // Ensure values are properly appended (not empty)
        formData.append("tieu_de", this.form.tieu_de.trim());
        formData.append("mo_ta", this.form.mo_ta ? this.form.mo_ta.trim() : "");
        formData.append("noi_dung", this.form.noi_dung.trim());
        formData.append("trang_thai", String(this.form.trang_thai));

        // Add file if present
        if (this.form.file) {
          formData.append("file", this.form.file);
        }

        // DEBUG: Log FormData contents
        console.log("📤 FormData being sent:");
        for (let [key, value] of formData.entries()) {
          if (value instanceof File) {
            console.log(`  ${key}: [File] ${value.name} (${value.size} bytes, type: ${value.type})`);
          } else {
            console.log(`  ${key}: "${value}" (type: ${typeof value}, length: ${String(value).length})`);
          }
        }

        let response;
        if (this.isEdit) {
          console.log("🔄 Updating lesson ID:", this.$route.params.id);
          response = await lessonApi.updateLesson(this.$route.params.id, formData);
          this.success = "Cập nhật bài học thành công!";
        } else {
          console.log("➕ Creating new lesson");
          response = await lessonApi.createLesson(formData);
          this.success = "Tạo bài học thành công!";
        }

        if (response && response.success) {
          // Redirect after 1.5 seconds
          setTimeout(() => {
            this.$router.push("/lessons");
          }, 1500);
        } else {
          this.error = response?.message || "Lỗi khi lưu bài học";
        }
      } catch (err) {
        this.error = err.message || "Lỗi khi lưu bài học";
        console.error("❌ Error saving lesson:", err);

        // DEBUG: Log full error response
        if (err.response) {
          console.error("Response status:", err.response.status);
          console.error("Response data:", err.response.data);
        }

        // DEBUG: Log validation errors if present
        if (err.response?.status === 422) {
          const errors = err.response.data?.errors || err.response.data;
          console.error("❌ Validation errors object:", errors);

          // Format error messages for display
          if (errors && typeof errors === 'object') {
            const errorMessages = Object.entries(errors)
              .map(([field, messages]) => {
                const fieldName = {
                  'tieu_de': 'Tiêu đề',
                  'mo_ta': 'Mô tả',
                  'noi_dung': 'Nội dung',
                  'trang_thai': 'Trạng thái',
                  'file': 'File'
                }[field] || field;

                const msgText = Array.isArray(messages) ? messages.join(', ') : messages;
                return `${fieldName}: ${msgText}`;
              })
              .join('\n');

            this.error = `Lỗi xác thực:\n${errorMessages}`;
          }
        }
      } finally {
        this.loading = false;
      }
    },
    async loadLesson(lessonId) {
      this.loading = true;
      this.error = null;

      try {
        const response = await lessonApi.getLessonForEdit(lessonId);

        if (response && response.success) {
          const lesson = response.data;
          this.form = {
            tieu_de: lesson.title,
            mo_ta: lesson.description || "",
            noi_dung: lesson.content,
            trang_thai: lesson.status,
            file: null,
          };

          // Store existing file info if present
          if (lesson.file && lesson.file.path) {
            this.existingFile = {
              name: lesson.file.path.split('/').pop(),
              size: lesson.file.size,
              type: lesson.file.type,
              url: lesson.file.url,
            };
          }
        } else {
          this.error = response.data.message || "Không thể tải bài học";
        }
      } catch (err) {
        this.error = "Không thể tải bài học";
        console.error("Error loading lesson:", err);
      } finally {
        this.loading = false;
      }
    },
  },
  mounted() {
    if (this.$route.params.id) {
      this.isEdit = true;
      this.loadLesson(this.$route.params.id);
    }
  },
};
</script>

<style scoped>
.lesson-editor-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.lesson-editor-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  border-bottom: 2px solid #ddd;
  padding-bottom: 15px;
}

.lesson-editor-header h1 {
  margin: 0;
  color: #333;
}

.lesson-form {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: bold;
  color: #333;
}

.form-group input,
.form-group textarea,
.form-group select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  font-family: inherit;
}

.form-group input[type="file"] {
  padding: 8px;
  cursor: pointer;
}

.form-group input[type="file"]::file-selector-button {
  padding: 6px 12px;
  background: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  margin-right: 10px;
}

.form-group input[type="file"]::file-selector-button:hover {
  background: #0056b3;
}

.file-info {
  display: block;
  margin-top: 8px;
  color: #28a745;
  font-size: 12px;
  font-weight: 500;
}

.form-group textarea {
  resize: vertical;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
}

.form-group small {
  display: block;
  margin-top: 5px;
  color: #999;
  font-size: 12px;
}

.error-message {
  background: #fee;
  color: #c33;
  padding: 10px 15px;
  border-radius: 4px;
  margin-bottom: 15px;
}

.success-message {
  background: #efe;
  color: #3c3;
  padding: 10px 15px;
  border-radius: 4px;
  margin-bottom: 15px;
}

.form-actions {
  display: flex;
  gap: 10px;
  margin-top: 30px;
}

.btn {
  padding: 12px 20px;
  border: none;
  border-radius: 4px;
  font-size: 14px;
  cursor: pointer;
  text-decoration: none;
  display: inline-block;
  transition: background 0.2s;
}

.btn-primary {
  background: #007bff;
  color: white;
  flex: 1;
}

.btn-primary:hover:not(:disabled) {
  background: #0056b3;
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background: #6c757d;
  color: white;
  flex: 1;
}

.btn-secondary:hover {
  background: #5a6268;
}
</style>
