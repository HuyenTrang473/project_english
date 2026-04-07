<template>
    <div class="admin-test-manager">
        <!-- Header với nút create -->
        <div class="manager-header">
            <h3>Quản lý Đề thi</h3>
            <button class="btn btn-primary" @click="openCreateDialog">
                <i class="fa-solid fa-plus" aria-hidden="true"></i> Tạo mới đề thi
            </button>
        </div>

        <!-- Search & Filter -->
        <div class="search-filter">
            <input v-model="searchQuery" type="text" class="search-input" placeholder="Tìm kiếm theo tên đề thi..."
                @input="handleSearch" />
            <select v-model="filterStatus" class="filter-select" @change="handleFilterChange">
                <option value="">Tất cả trạng thái</option>
                <option value="1">Bản nháp</option>
                <option value="2">Đã xuất bản</option>
            </select>
            <button class="btn btn-secondary" @click="loadTests">
                <i class="fa-solid fa-rotate-right" aria-hidden="true"></i> Tải lại
            </button>
        </div>

        <!-- Loading & Error States -->
        <div v-if="loading" class="loading-state">
            <p>Đang tải dữ liệu...</p>
        </div>

        <div v-if="error" class="error-state">
            <p>{{ error }}</p>
            <button class="btn btn-secondary" @click="loadTests">Thử lại</button>
        </div>

        <!-- Tests Table -->
        <div v-if="!loading && !error && tests.length > 0" class="tests-table-wrapper">
            <table class="tests-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên đề thi</th>
                        <th>Giáo viên</th>
                        <th>Bài học</th>
                        <th>Thời gian (phút)</th>
                        <th>Điểm tối đa</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="test in tests" :key="test.id" class="table-row">
                        <td class="id-cell">{{ test.id }}</td>
                        <td class="name-cell">{{ test.ten_bai_test }}</td>
                        <td>{{ test.giao_vien?.name || 'N/A' }}</td>
                        <td>{{ test.lesson?.tieu_de || 'N/A' }}</td>
                        <td class="center">{{ test.thoi_gian_toi_da }}</td>
                        <td class="center">{{ test.diem_tong_max }}</td>
                        <td class="center">
                            <span :class="['status-badge', getStatusClass(test.trang_thai)]">
                                {{ getStatusText(test.trang_thai) }}
                            </span>
                        </td>
                        <td class="date-cell">{{ formatDate(test.created_at) }}</td>
                        <td class="actions-cell">
                            <button class="btn btn-sm btn-primary" @click="viewTest(test.id)" title="Xem">
                                <i class="fa-regular fa-eye" aria-hidden="true"></i>
                            </button>
                            <button class="btn btn-sm btn-info" @click="openEditDialog(test)" title="Chỉnh sửa">
                                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" @click="deleteTest(test.id)" title="Xóa">
                                <i class="fa-solid fa-trash" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <div v-if="!loading && !error && tests.length === 0" class="empty-state">
            <p>Không có đề thi nào</p>
        </div>

        <!-- Pagination -->
        <div v-if="pagination" class="pagination">
            <button v-if="pagination.current_page > 1" class="btn btn-secondary" @click="previousPage">
                ← Trước
            </button>
            <span class="page-info">
                Trang {{ pagination.current_page }} / {{ pagination.last_page }}
            </span>
            <button v-if="pagination.current_page < pagination.last_page" class="btn btn-secondary" @click="nextPage">
                Tiếp →
            </button>
        </div>

        <!-- Create/Edit Dialog -->
        <div v-if="showDialog" class="modal-overlay" @click.self="closeDialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>{{ isEditMode ? 'Cập nhật đề thi' : 'Tạo mới đề thi' }}</h4>
                    <button class="close-btn" @click="closeDialog">×</button>
                </div>

                <div class="modal-body">
                    <form @submit.prevent="saveTest">
                        <!-- Teacher Selection -->
                        <div v-if="isAdmin" class="form-group">
                            <label>Giáo viên *</label>
                            <select v-model="formData.id_giao_vien" class="form-input" required>
                                <option value="">-- Chọn giáo viên --</option>
                                <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                                    {{ teacher.name }}
                                </option>
                            </select>
                            <span v-if="errors.id_giao_vien" class="error-message">
                                {{ errors.id_giao_vien[0] }}
                            </span>
                        </div>
                        <div v-else class="form-group">
                            <label>Giáo viên</label>
                            <input :value="authStore.user?.name || 'Bạn'" type="text" class="form-input" disabled />
                        </div>

                        <!-- Lesson Selection -->
                        <div class="form-group">
                            <label>Bài học *</label>
                            <select v-model="formData.id_lesson" class="form-input" required>
                                <option value="">-- Chọn bài học --</option>
                                <option v-for="lesson in lessons" :key="lesson.id" :value="lesson.id">
                                    {{ lesson.tieu_de }}
                                </option>
                            </select>
                            <span v-if="errors.id_lesson" class="error-message">
                                {{ errors.id_lesson[0] }}
                            </span>
                        </div>

                        <!-- Test Name -->
                        <div class="form-group">
                            <label>Tên đề thi *</label>
                            <input v-model="formData.ten_bai_test" type="text" class="form-input"
                                placeholder="Nhập tên đề thi" required />
                            <span v-if="errors.ten_bai_test" class="error-message">
                                {{ errors.ten_bai_test[0] }}
                            </span>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label>Mô tả</label>
                            <textarea v-model="formData.mo_ta" class="form-input form-textarea"
                                placeholder="Nhập mô tả về đề thi" rows="3"></textarea>
                            <span v-if="errors.mo_ta" class="error-message">
                                {{ errors.mo_ta[0] }}
                            </span>
                        </div>

                        <!-- Max Time -->
                        <div class="form-row">
                            <div class="form-group">
                                <label>Thời gian tối đa (phút) *</label>
                                <input v-model="formData.thoi_gian_toi_da" type="number" class="form-input" min="1"
                                    max="1440" required />
                                <span v-if="errors.thoi_gian_toi_da" class="error-message">
                                    {{ errors.thoi_gian_toi_da[0] }}
                                </span>
                            </div>

                            <!-- Max Score -->
                            <div class="form-group">
                                <label>Điểm tối đa *</label>
                                <input v-model="formData.diem_tong_max" type="number" class="form-input" min="0.01"
                                    max="10000" step="0.01" required />
                                <span v-if="errors.diem_tong_max" class="error-message">
                                    {{ errors.diem_tong_max[0] }}
                                </span>
                            </div>
                        </div>

                        <!-- Max Attempts -->
                        <div class="form-row">
                            <div class="form-group">
                                <label>Số lần làm tối đa</label>
                                <input v-model="formData.so_lan_lam_toi_da" type="number" class="form-input" min="1"
                                    max="100" />
                            </div>

                            <!-- Status -->
                            <div class="form-group">
                                <label>Trạng thái *</label>
                                <select v-model="formData.trang_thai" class="form-input" required>
                                    <option value="1">Bản nháp</option>
                                    <option value="2">Đã xuất bản</option>
                                </select>
                                <span v-if="errors.trang_thai" class="error-message">
                                    {{ errors.trang_thai[0] }}
                                </span>
                            </div>
                        </div>

                        <!-- Checkboxes -->
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input v-model="formData.co_xao_tron_cau_hoi" type="checkbox" />
                                Xáo trộn câu hỏi
                            </label>
                            <label class="checkbox-label">
                                <input v-model="formData.co_xao_tron_dap_an" type="checkbox" />
                                Xáo trộn đáp án
                            </label>
                            <label class="checkbox-label">
                                <input v-model="formData.hien_thi_ket_qua_ngay_lap" type="checkbox" />
                                Hiển thị kết quả ngay lập tức
                            </label>
                            <label class="checkbox-label">
                                <input v-model="formData.hien_thi_dap_an_dung" type="checkbox" />
                                Hiển thị đáp án đúng
                            </label>
                            <label class="checkbox-label">
                                <input v-model="formData.cho_xem_lai_test" type="checkbox" />
                                Cho xem lại bài thi
                            </label>
                        </div>

                        <!-- Start Date -->
                        <div class="form-group">
                            <label>Ngày bắt đầu</label>
                            <input v-model="formData.ngay_bat_dau" type="datetime-local" class="form-input" />
                        </div>

                        <!-- End Date -->
                        <div class="form-group">
                            <label>Ngày kết thúc</label>
                            <input v-model="formData.ngay_ket_thuc" type="datetime-local" class="form-input" />
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary" :disabled="submitting">
                                {{ submitting ? 'Đang lưu...' : 'Lưu' }}
                            </button>
                            <button type="button" class="btn btn-secondary" @click="closeDialog">
                                Hủy
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import http from '@/api/axiosClient';
import { useAuthStore } from '@/stores/auth';

export default {
    name: 'AdminTestManager',
    setup() {
        const authStore = useAuthStore();
        return { authStore };
    },
    data() {
        return {
            tests: [],
            teachers: [],
            lessons: [],
            loading: false,
            error: null,
            showDialog: false,
            isEditMode: false,
            submitting: false,
            searchQuery: '',
            filterStatus: '',
            currentPage: 1,
            pagination: null,
            errors: {},
            formData: {
                id_giao_vien: '',
                id_lesson: '',
                ten_bai_test: '',
                mo_ta: '',
                thoi_gian_toi_da: 60,
                diem_tong_max: 100,
                trang_thai: 1,
                so_lan_lam_toi_da: 1,
                co_xao_tron_cau_hoi: false,
                co_xao_tron_dap_an: false,
                hien_thi_ket_qua_ngay_lap: true,
                hien_thi_dap_an_dung: true,
                cho_xem_lai_test: true,
                ngay_bat_dau: '',
                ngay_ket_thuc: '',
            },
        };
    },
    mounted() {
        this.loadTests();
        this.loadTeachers();
        this.loadLessons();
    },
    computed: {
        isAdmin() {
            return this.authStore?.isAdmin;
        },
    },
    methods: {
        normalizeDateTime(value) {
            if (!value) return null;
            // Convert datetime-local format (YYYY-MM-DDTHH:mm) to backend format (YYYY-MM-DD HH:mm:ss)
            return `${value.replace('T', ' ')}:00`;
        },
        async loadTests() {
            this.loading = true;
            this.error = null;
            try {
                const response = await http.get('/admin/tests', {
                    params: {
                        page: this.currentPage,
                        search: this.searchQuery,
                        status: this.filterStatus || undefined,
                        per_page: 15,
                    },
                });
                if (response.success) {
                    this.tests = response.data || [];
                    this.pagination = response.pagination || null;
                } else {
                    this.error = response?.message || 'Lỗi khi tải danh sách đề thi';
                }
            } catch (err) {
                this.error = 'Lỗi khi tải danh sách đề thi';
                console.error(err);
            } finally {
                this.loading = false;
            }
        },
        async loadTeachers() {
            if (!this.isAdmin) {
                this.teachers = this.authStore?.user ? [this.authStore.user] : [];
                return;
            }
            try {
                const response = await http.get('/admin/teachers');
                if (response?.success && response?.data) {
                    this.teachers = response.data;
                }
            } catch (err) {
                console.error('Lỗi khi tải danh sách giáo viên:', err);
            }
        },
        async loadLessons() {
            try {
                const response = await http.get('/admin/lessons');
                if (response?.success && Array.isArray(response?.data)) {
                    this.lessons = response.data;
                }
            } catch (err) {
                console.error('Lỗi khi tải danh sách bài học:', err);
            }
        },
        handleSearch() {
            this.currentPage = 1;
            this.loadTests();
        },
        handleFilterChange() {
            this.currentPage = 1;
            this.loadTests();
        },
        nextPage() {
            if (this.pagination && this.currentPage < this.pagination.last_page) {
                this.currentPage++;
                this.loadTests();
            }
        },
        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.loadTests();
            }
        },
        openCreateDialog() {
            this.isEditMode = false;
            this.resetForm();
            this.showDialog = true;
        },
        openEditDialog(test) {
            this.isEditMode = true;
            this.formData = {
                id_giao_vien: test.id_giao_vien,
                id_lesson: test.id_lesson,
                ten_bai_test: test.ten_bai_test,
                mo_ta: test.mo_ta,
                thoi_gian_toi_da: test.thoi_gian_toi_da,
                diem_tong_max: test.diem_tong_max,
                trang_thai: test.trang_thai,
                so_lan_lam_toi_da: test.so_lan_lam_toi_da,
                co_xao_tron_cau_hoi: test.co_xao_tron_cau_hoi,
                co_xao_tron_dap_an: test.co_xao_tron_dap_an,
                hien_thi_ket_qua_ngay_lap: test.hien_thi_ket_qua_ngay_lap,
                hien_thi_dap_an_dung: test.hien_thi_dap_an_dung,
                cho_xem_lai_test: test.cho_xem_lai_test,
                ngay_bat_dau: test.ngay_bat_dau ? test.ngay_bat_dau.slice(0, 16) : '',
                ngay_ket_thuc: test.ngay_ket_thuc ? test.ngay_ket_thuc.slice(0, 16) : '',
            };
            this.currentEditId = test.id;
            this.showDialog = true;
        },
        viewTest(testId) {
            this.$router.push(`/tests/${testId}/view`);
        },
        closeDialog() {
            this.showDialog = false;
            this.resetForm();
            this.errors = {};
        },
        resetForm() {
            this.formData = {
                id_giao_vien: this.isAdmin ? '' : this.authStore?.user?.id,
                id_lesson: '',
                ten_bai_test: '',
                mo_ta: '',
                thoi_gian_toi_da: 60,
                diem_tong_max: 100,
                trang_thai: 1,
                so_lan_lam_toi_da: 1,
                co_xao_tron_cau_hoi: false,
                co_xao_tron_dap_an: false,
                hien_thi_ket_qua_ngay_lap: true,
                hien_thi_dap_an_dung: true,
                cho_xem_lai_test: true,
                ngay_bat_dau: '',
                ngay_ket_thuc: '',
            };
            this.currentEditId = null;
        },
        async saveTest() {
            this.submitting = true;
            this.errors = {};
            try {
                const teacherId = this.isAdmin ? this.formData.id_giao_vien : this.authStore?.user?.id;
                const payload = {
                    ...this.formData,
                    id_giao_vien: parseInt(teacherId),
                    id_lesson: parseInt(this.formData.id_lesson),
                    thoi_gian_toi_da: parseInt(this.formData.thoi_gian_toi_da),
                    diem_tong_max: parseFloat(this.formData.diem_tong_max),
                    trang_thai: parseInt(this.formData.trang_thai),
                    so_lan_lam_toi_da: parseInt(this.formData.so_lan_lam_toi_da),
                    ngay_bat_dau: this.normalizeDateTime(this.formData.ngay_bat_dau),
                    ngay_ket_thuc: this.normalizeDateTime(this.formData.ngay_ket_thuc),
                };

                if (!Number.isInteger(payload.id_giao_vien) || !Number.isInteger(payload.id_lesson)) {
                    alert('Vui lòng chọn đầy đủ giáo viên và bài học.');
                    return;
                }

                if (this.isEditMode) {
                    const response = await http.put(`/admin/tests/${this.currentEditId}`, payload);
                    if (response.success) {
                        alert('Cập nhật đề thi thành công!');
                        this.closeDialog();
                        this.loadTests();
                    }
                } else {
                    const response = await http.post('/admin/tests', payload);
                    if (response.success) {
                        alert('Tạo mới đề thi thành công!');
                        this.closeDialog();
                        this.loadTests();
                    }
                }
            } catch (err) {
                if (err.response && err.response.data && err.response.data.errors) {
                    this.errors = err.response.data.errors;
                } else {
                    alert('Lỗi khi lưu đề thi: ' + (err.response?.data?.message || err.message));
                }
            } finally {
                this.submitting = false;
            }
        },
        async deleteTest(testId) {
            if (!confirm('Bạn có chắc chắn muốn xóa đề thi này?')) {
                return;
            }
            try {
                const response = await http.delete(`/admin/tests/${testId}`);
                if (response.success) {
                    alert('Xóa đề thi thành công!');
                    this.loadTests();
                }
            } catch (err) {
                alert('Lỗi khi xóa đề thi: ' + (err.response?.data?.message || err.message));
            }
        },
        getStatusText(status) {
            return status === 1 ? 'Bản nháp' : 'Đã xuất bản';
        },
        getStatusClass(status) {
            return status === 1 ? 'status-draft' : 'status-published';
        },
        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
            });
        },
    },
};
</script>

<style scoped>
.admin-test-manager {
    padding: 1.5rem;
}

.manager-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.manager-header h3 {
    margin: 0;
    font-size: 1.5rem;
    color: #1e293b;
}

.search-filter {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.search-input,
.filter-select {
    padding: 0.5rem 0.75rem;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 0.9rem;
}

.search-input {
    flex: 1;
    min-width: 250px;
}

.loading-state,
.error-state,
.empty-state {
    text-align: center;
    padding: 2rem;
    background: #fff;
    border-radius: 8px;
    color: #64748b;
}

.error-state {
    background: #fee2e2;
    color: #991b1b;
}

.error-state button {
    margin-top: 1rem;
}

.tests-table-wrapper {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}

.tests-table {
    width: 100%;
    border-collapse: collapse;
}

.tests-table thead {
    background: #f1f5f9;
    border-bottom: 2px solid #e2e8f0;
}

.tests-table th {
    padding: 0.75rem;
    text-align: left;
    font-weight: 600;
    color: #475569;
    font-size: 0.85rem;
}

.tests-table td {
    padding: 0.75rem;
    border-bottom: 1px solid #e2e8f0;
    font-size: 0.9rem;
}

.table-row:hover {
    background: #f8fafc;
}

.id-cell,
.center {
    text-align: center;
}

.name-cell {
    font-weight: 600;
    color: #1e293b;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-draft {
    background: #fef3c7;
    color: #92400e;
}

.status-published {
    background: #dcfce7;
    color: #166534;
}

.actions-cell {
    text-align: center;
    white-space: nowrap;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s;
}

.btn i {
    margin-right: 0.35rem;
}

.actions-cell .btn i {
    margin-right: 0;
}

.btn-primary {
    background: #3b82f6;
    color: #fff;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-secondary {
    background: #64748b;
    color: #fff;
}

.btn-secondary:hover {
    background: #475569;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.85rem;
}

.btn-info {
    background: #06b6d4;
    color: #fff;
}

.btn-info:hover {
    background: #0891b2;
}

.btn-danger {
    background: #ef4444;
    color: #fff;
}

.btn-danger:hover {
    background: #dc2626;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-top: 1.5rem;
}

.page-info {
    color: #64748b;
    font-size: 0.9rem;
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
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.modal-header h4 {
    margin: 0;
    color: #1e293b;
}

.close-btn {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #64748b;
}

.modal-body {
    padding: 1.5rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #1e293b;
    font-size: 0.9rem;
}

.form-input,
.form-textarea {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 0.9rem;
    font-family: inherit;
}

.form-input:focus,
.form-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-textarea {
    resize: vertical;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    font-weight: normal;
    cursor: pointer;
}

.checkbox-label input[type='checkbox'] {
    cursor: pointer;
}

.error-message {
    display: block;
    color: #dc2626;
    font-size: 0.8rem;
    margin-top: 0.25rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
    padding-top: 1.5rem;
}

.form-actions button {
    flex: 1;
}

@media (max-width: 768px) {
    .search-filter {
        flex-direction: column;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .tests-table {
        font-size: 0.85rem;
    }

    .tests-table th,
    .tests-table td {
        padding: 0.5rem;
    }
}
</style>
