<template>
  <div class="teacher-manager">
    <!-- Controls -->
    <div class="controls-bar">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="🔍 Tìm kiếm giáo viên..."
        class="search-input"
      />
      <button @click="showAddForm = true" class="btn btn-primary">
        ➕ Thêm Giáo Viên
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading">⏳ Đang tải...</div>

    <!-- Error Message -->
    <div v-if="error" class="error-message">
      ⚠️ {{ error }}
    </div>

    <!-- Teachers Table -->
    <div v-if="!loading && teachers.length > 0" class="teachers-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Tên Giáo Viên</th>
            <th>Email</th>
            <th>Trạng Thái</th>
            <th>Bài Học</th>
            <th>Thao Tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="teacher in filteredTeachers" :key="teacher.id">
            <td>{{ teacher.id }}</td>
            <td>{{ teacher.name }}</td>
            <td>{{ teacher.email }}</td>
            <td>
              <span :class="teacher.active ? 'status-active' : 'status-inactive'">
                {{ teacher.active ? '✅ Hoạt động' : '⏸️ Vô hiệu' }}
              </span>
            </td>
            <td>{{ teacher.lessonCount || 0 }} bài</td>
            <td>
              <button @click="editTeacher(teacher)" class="btn btn-sm btn-edit">
                ✏️ Sửa
              </button>
              <button @click="deleteTeacher(teacher.id)" class="btn btn-sm btn-delete">
                🗑️ Xóa
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Empty State -->
    <div v-else-if="!loading" class="empty-state">
      <p>Chưa có giáo viên nào trong hệ thống</p>
    </div>

    <!-- Add/Edit Form Modal -->
    <div v-if="showAddForm" class="modal-overlay">
      <div class="modal-content">
        <div class="modal-header">
          <h3>{{ editingTeacher ? '✏️ Chỉnh Sửa Giáo Viên' : '➕ Thêm Giáo Viên Mới' }}</h3>
          <button @click="closeForm" class="btn-close">✕</button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label>Tên Giáo Viên *</label>
            <input v-model="formData.name" type="text" placeholder="Nhập tên giáo viên" />
          </div>

          <div class="form-group">
            <label>Email *</label>
            <input v-model="formData.email" type="email" placeholder="Nhập email" />
          </div>

          <div class="form-group">
            <label>Mật Khẩu {{ editingTeacher ? '(để trống nếu không đổi)' : '*' }}</label>
            <input v-model="formData.password" type="password" placeholder="Nhập mật khẩu (tối thiểu 8 ký tự)" />
          </div>

          <div class="form-group">
            <label>
              <input v-model="formData.active" type="checkbox" />
              ✅ Hoạt động
            </label>
          </div>
        </div>

        <div class="modal-footer">
          <button @click="closeForm" class="btn btn-secondary">
            ❌ Hủy
          </button>
          <button @click="saveTeacher" class="btn btn-primary">
            💾 Lưu
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import http from '@/api/axiosClient';

export default {
  name: 'AdminTeacherManager',
  data() {
    return {
      teachers: [],
      loading: false,
      error: null,
      searchQuery: '',
      showAddForm: false,
      editingTeacher: null,
      formData: {
        name: '',
        email: '',
        password: '',
        active: true,
      },
    };
  },
  computed: {
    filteredTeachers() {
      if (!this.searchQuery) return this.teachers;
      
      const query = this.searchQuery.toLowerCase();
      return this.teachers.filter(
        teacher =>
          teacher.name.toLowerCase().includes(query) ||
          teacher.email.toLowerCase().includes(query)
      );
    },
  },
  mounted() {
    this.loadTeachers();
  },
  methods: {
    async loadTeachers() {
      this.loading = true;
      this.error = null;

      try {
        const response = await http.get('/admin/teachers');
        
        if (response && response.success) {
          this.teachers = response.data || [];
        } else {
          this.error = response?.message || 'Lỗi khi tải danh sách giáo viên';
        }
      } catch (err) {
        console.error('Error loading teachers:', err);
        this.error = err?.response?.data?.message || 'Lỗi khi tải danh sách giáo viên';
      } finally {
        this.loading = false;
      }
    },

    editTeacher(teacher) {
      this.editingTeacher = teacher;
      this.formData = {
        name: teacher.name,
        email: teacher.email,
        password: '',
        active: teacher.active !== false,
      };
      this.showAddForm = true;
    },

    closeForm() {
      this.showAddForm = false;
      this.editingTeacher = null;
      this.formData = {
        name: '',
        email: '',
        password: '',
        active: true,
      };
    },

    async saveTeacher() {
      // Validation
      if (!this.formData.name.trim()) {
        this.error = 'Vui lòng nhập tên giáo viên';
        return;
      }
      if (!this.formData.email.trim()) {
        this.error = 'Vui lòng nhập email';
        return;
      }
      if (!this.editingTeacher && !this.formData.password) {
        this.error = 'Vui lòng nhập mật khẩu cho giáo viên mới';
        return;
      }

      try {
        const url = this.editingTeacher
          ? `/admin/teachers/${this.editingTeacher.id}`
          : '/admin/teachers';

        const method = this.editingTeacher ? 'put' : 'post';

        const payload = {
          name: this.formData.name,
          email: this.formData.email,
          active: this.formData.active ? 1 : 0,
        };

        if (this.formData.password) {
          payload.password = this.formData.password;
        }

        const response = await (method === 'put'
          ? http.put(url, payload)
          : http.post(url, payload));

        if (response && response.success) {
          alert(this.editingTeacher ? 'Cập nhật thành công!' : 'Thêm giáo viên thành công!');
          this.closeForm();
          this.loadTeachers();
        } else {
          this.error = response?.message || 'Lỗi khi lưu giáo viên';
        }
      } catch (err) {
        console.error('Error saving teacher:', err);
        this.error = err?.response?.data?.message || 'Lỗi khi lưu giáo viên';
      }
    },

    async deleteTeacher(teacherId) {
      if (!confirm('Bạn chắc chắn muốn xóa giáo viên này?')) return;

      try {
        const response = await http.delete(`/admin/teachers/${teacherId}`);

        if (response && response.success) {
          alert('Xóa thành công!');
          this.loadTeachers();
        } else {
          this.error = response?.message || 'Lỗi khi xóa giáo viên';
        }
      } catch (err) {
        console.error('Error deleting teacher:', err);
        this.error = err?.response?.data?.message || 'Lỗi khi xóa giáo viên';
      }
    },
  },
};
</script>

<style scoped>
.teacher-manager {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
}

.controls-bar {
  display: flex;
  gap: 10px;
  padding: 1.5rem;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
  flex-wrap: wrap;
}

.search-input {
  flex: 1;
  min-width: 250px;
  padding: 10px 15px;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  font-size: 14px;
}

.search-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.loading,
.empty-state,
.error-message {
  padding: 2rem;
  text-align: center;
}

.error-message {
  background: #fee;
  color: #c33;
  font-weight: 500;
}

.teachers-table {
  overflow-x: auto;
}

.teachers-table table {
  width: 100%;
  border-collapse: collapse;
}

.teachers-table thead {
  background: #f8fafc;
  border-bottom: 2px solid #e2e8f0;
}

.teachers-table th {
  padding: 12px 16px;
  text-align: left;
  font-weight: 600;
  color: #334155;
  font-size: 13px;
}

.teachers-table td {
  padding: 12px 16px;
  border-bottom: 1px solid #e2e8f0;
  color: #475569;
  font-size: 14px;
}

.teachers-table tbody tr:hover {
  background: #f8fafc;
}

.status-active {
  display: inline-block;
  padding: 4px 8px;
  background: #dcfce7;
  color: #166534;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
}

.status-inactive {
  display: inline-block;
  padding: 4px 8px;
  background: #ffe2e5;
  color: #991b1b;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: #fff;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  max-height: 80vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px rgba(0, 0, 0, 0.15);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e2e8f0;
}

.modal-header h3 {
  margin: 0;
  font-size: 1.1rem;
}

.btn-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #94a3b8;
}

.btn-close:hover {
  color: #333;
}

.modal-body {
  padding: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 6px;
  font-weight: 500;
  color: #334155;
  font-size: 14px;
}

.form-group input[type='text'],
.form-group input[type='email'],
.form-group input[type='password'] {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  font-size: 14px;
  font-family: inherit;
}

.form-group input[type='text']:focus,
.form-group input[type='email']:focus,
.form-group input[type='password']:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-group input[type='checkbox'] {
  margin-right: 8px;
  cursor: pointer;
}

.modal-footer {
  display: flex;
  gap: 10px;
  padding: 1.5rem;
  border-top: 1px solid #e2e8f0;
  justify-content: flex-end;
}

/* Buttons */
.btn {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
}

.btn-primary {
  background: #3b82f6;
  color: white;
}

.btn-primary:hover {
  background: #2563eb;
}

.btn-secondary {
  background: #e2e8f0;
  color: #334155;
}

.btn-secondary:hover {
  background: #cbd5e1;
}

.btn-sm {
  padding: 4px 8px;
  font-size: 12px;
}

.btn-edit {
  background: #fbbf24;
  color: #000;
}

.btn-edit:hover {
  background: #f59e0b;
}

.btn-delete {
  background: #ef4444;
  color: white;
}

.btn-delete:hover {
  background: #dc2626;
}

@media (max-width: 768px) {
  .controls-bar {
    flex-direction: column;
  }

  .search-input {
    min-width: 100%;
  }

  .teachers-table table {
    font-size: 13px;
  }

  .teachers-table th,
  .teachers-table td {
    padding: 8px 12px;
  }
}
</style>
