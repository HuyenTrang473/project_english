<template>
  <div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <h1 class="mb-0">📋 Danh Sách Bài Test</h1>

      <!-- Action Buttons -->
      <div class="d-flex gap-2 flex-wrap">
        <!-- View Mode Toggle -->
        <div class="btn-group" role="group">
          <button type="button" :class="['btn', viewMode === 'grid' ? 'btn-primary' : 'btn-outline-secondary']"
            @click="viewMode = 'grid'" title="Xem dạng lưới">
            <i class="fa fa-th"></i>
          </button>
          <button type="button" :class="['btn', viewMode === 'list' ? 'btn-primary' : 'btn-outline-secondary']"
            @click="viewMode = 'list'" title="Xem dạng danh sách">
            <i class="fa fa-list"></i>
          </button>
        </div>

        <!-- Refresh Button -->
        <button @click="refreshData" :disabled="loading" class="btn btn-outline-secondary" title="Tải lại dữ liệu">
          <i :class="['fa fa-refresh', { 'fa-spin': loading }]"></i>
        </button>

        <!-- Export Button -->
        <button @click="exportData" :disabled="tests.length === 0" class="btn btn-outline-info"
          title="Xuất dữ liệu CSV">
          <i class="fa fa-download"></i> Xuất
        </button>

        <!-- Settings Button -->
        <button @click="showSettings = !showSettings" class="btn btn-outline-secondary" title="Cài đặt">
          <i class="fa fa-cog"></i>
        </button>

        <!-- Create New Test Button -->
        <router-link v-if="isTeacher || isAdmin" to="/tests/create-template" class="btn btn-primary">
          <i class="fa fa-plus"></i> Tạo Mới
        </router-link>
      </div>
    </div>

    <!-- Settings Panel -->
    <div v-if="showSettings" class="alert alert-light border mb-4">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0">⚙️ Cài đặt hiển thị</h6>
        <button @click="showSettings = false" class="btn-close"></button>
      </div>
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Số bản ghi/trang</label>
          <select v-model.number="pageSize" class="form-select form-select-sm" @change="updatePageSize">
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="20">20</option>
            <option value="30">30</option>
          </select>
        </div>
        <div class="col-md-9">
          <label class="form-label">Hiển thị thông tin</label>
          <div class="d-flex gap-2 flex-wrap">
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="showTeacher" v-model="showInfo.teacher">
              <label class="form-check-label" for="showTeacher">Giáo viên</label>
            </div>
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="showTime" v-model="showInfo.time">
              <label class="form-check-label" for="showTime">Thời gian</label>
            </div>
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="showScore" v-model="showInfo.score">
              <label class="form-check-label" for="showScore">Điểm</label>
            </div>
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="showStatus" v-model="showInfo.status">
              <label class="form-check-label" for="showStatus">Trạng thái</label>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <input v-model="filters.search" type="text" class="form-control" placeholder="Tìm kiếm theo tên bài test..."
              @input="onSearchChange">
          </div>
          <div class="col-md-3">
            <select v-model="filters.status" class="form-select" @change="onFilterChange">
              <option :value="null">Tất cả trạng thái</option>
              <option value="1">Nháp</option>
              <option value="2">Đã Công Bố</option>
            </select>
          </div>
          <div class="col-md-3">
            <select v-model="filters.sortBy" class="form-select" @change="onFilterChange">
              <option value="created_at">Mới Nhất</option>
              <option value="ten_bai_test">Tên (A-Z)</option>
              <option value="updated_at">Cập Nhật Gần Đây</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Đang tải...</span>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="tests.length === 0" class="alert alert-info text-center">
      <i class="fa fa-inbox"></i> Không có bài test nào
    </div>

    <!-- Tests List - Grid View -->
    <div v-if="!loading && tests.length > 0 && viewMode === 'grid'" class="row">
      <div v-for="test in tests" :key="test.id" class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 shadow-sm hover-card">
          <!-- Card Header -->
          <div class="card-header bg-primary text-white p-3">
            <h5 class="card-title mb-0 text-truncate">{{ test.ten_bai_test }}</h5>
          </div>

          <!-- Card Body -->
          <div class="card-body">
            <!-- Test Info -->
            <div class="mb-3">
              <p v-if="showInfo.teacher" class="text-muted small mb-1">
                <i class="fa fa-user"></i> {{ test.giao_vien?.name || 'Unknown' }}
              </p>
              <p v-if="showInfo.time" class="text-muted small mb-2">
                <i class="fa fa-clock"></i>
                {{ test.thoi_gian_toi_da }} phút
              </p>
              <p v-if="showInfo.score" class="text-muted small mb-0">
                <i class="fa fa-trophy"></i>
                Điểm tối đa: {{ test.diem_tong_max }} điểm
              </p>
            </div>

            <!-- Description -->
            <p class="card-text small">{{ truncate(test.mo_ta, 80) }}</p>

            <!-- Status Badge -->
            <div v-if="showInfo.status" class="mb-3">
              <span :class="[
                'badge',
                test.trang_thai === 2 ? 'bg-success' : 'bg-secondary'
              ]">
                {{ test.trang_thai === 2 ? '✓ Công Bố' : '✎ Nháp' }}
              </span>
            </div>
          </div>

          <!-- Card Footer -->
          <div class="card-footer bg-light p-3">
            <div class="d-flex gap-2 flex-wrap">
              <router-link :to="`/tests/${test.id}/view`" class="btn btn-sm btn-info flex-grow-1">
                <i class="fa fa-eye"></i> Xem
              </router-link>

              <router-link v-if="(isTeacher || isAdmin) && isOwner(test.id_giao_vien)" :to="`/tests/${test.id}/edit`"
                class="btn btn-sm btn-warning" title="Chỉnh sửa">
                <i class="fa fa-edit"></i>
              </router-link>

              <button v-if="(isTeacher || isAdmin) && isOwner(test.id_giao_vien)" @click="onDeleteTest(test.id)"
                class="btn btn-sm btn-danger" :disabled="loading" title="Xóa">
                <i class="fa fa-trash"></i>
              </button>


            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tests List - List View -->
    <div v-if="!loading && tests.length > 0 && viewMode === 'list'" class="table-responsive">
      <table class="table table-hover table-striped">
        <thead class="table-dark sticky-top">
          <tr>
            <th>Tên Bài Test</th>
            <th v-if="showInfo.teacher">Giáo Viên</th>
            <th v-if="showInfo.time">Thời Gian</th>
            <th v-if="showInfo.score">Điểm</th>
            <th v-if="showInfo.status">Trạng Thái</th>
            <th class="text-center">Thao Tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="test in tests" :key="test.id">
            <td>
              <strong>{{ test.ten_bai_test }}</strong>
              <br>
              <small class="text-muted">{{ truncate(test.mo_ta, 50) }}</small>
            </td>
            <td v-if="showInfo.teacher" class="text-muted small">
              {{ test.giao_vien?.name || 'Unknown' }}
            </td>
            <td v-if="showInfo.time" class="text-muted small">
              {{ test.thoi_gian_toi_da }} phút
            </td>
            <td v-if="showInfo.score" class="text-muted small">
              {{ test.diem_tong_max }} điểm
            </td>
            <td v-if="showInfo.status">
              <span :class="[
                'badge',
                test.trang_thai === 2 ? 'bg-success' : 'bg-secondary'
              ]">
                {{ test.trang_thai === 2 ? '✓ Công Bố' : '✎ Nháp' }}
              </span>
            </td>
            <td class="text-center">
              <div class="btn-group btn-group-sm" role="group">
                <router-link :to="`/tests/${test.id}/view`" class="btn btn-info" title="Xem">
                  <i class="fa fa-eye"></i>
                </router-link>

                <router-link v-if="(isTeacher || isAdmin) && isOwner(test.id_giao_vien)" :to="`/tests/${test.id}/edit`"
                  class="btn btn-warning" title="Chỉnh sửa">
                  <i class="fa fa-edit"></i>
                </router-link>

                <button v-if="(isTeacher || isAdmin) && isOwner(test.id_giao_vien)" @click="onDeleteTest(test.id)"
                  class="btn btn-danger" :disabled="loading" title="Xóa">
                  <i class="fa fa-trash"></i>
                </button>


              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <nav v-if="pagination.lastPage > 1" class="mt-4">
      <ul class="pagination justify-content-center">
        <li :class="['page-item', { disabled: pagination.currentPage === 1 }]">
          <button class="page-link" @click="goToPage(pagination.currentPage - 1)"
            :disabled="pagination.currentPage === 1">
            Trước
          </button>
        </li>

        <li v-for="page in pageNumbers" :key="page" :class="['page-item', { active: page === pagination.currentPage }]">
          <button class="page-link" @click="goToPage(page)">
            {{ page }}
          </button>
        </li>

        <li :class="['page-item', { disabled: pagination.currentPage === pagination.lastPage }]">
          <button class="page-link" @click="goToPage(pagination.currentPage + 1)"
            :disabled="pagination.currentPage === pagination.lastPage">
            Tiếp
          </button>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useTestStore } from "@/stores/test";
import { useAuthStore } from "@/stores/auth";

const router = useRouter();
const testStore = useTestStore();
const authStore = useAuthStore();

// State
const searchTimeout = ref(null);
const viewMode = ref('grid'); // 'grid' or 'list'
const showSettings = ref(false);
const pageSize = ref(15);
const showInfo = ref({
  teacher: true,
  time: true,
  score: true,
  status: true
});

// Computed
const tests = computed(() => testStore.tests);
const loading = computed(() => testStore.loading);
const pagination = computed(() => testStore.pagination);
const filters = computed(() => testStore.filters);
const isTeacher = computed(() => authStore.user?.role === "giao_vien");
const isAdmin = computed(() => authStore.user?.role === "admin");

// Pagination Numbers
const pageNumbers = computed(() => {
  const total = pagination.value.lastPage;
  const current = pagination.value.currentPage;
  const max = 5;

  let start = Math.max(1, current - Math.floor(max / 2));
  let end = Math.min(total, start + max - 1);

  if (end - start < max - 1) {
    start = Math.max(1, end - max + 1);
  }

  return Array.from({ length: end - start + 1 }, (_, i) => start + i);
});

// Methods
const truncate = (text, length) => {
  if (!text) return "";
  return text.length > length ? text.substring(0, length) + "..." : text;
};

const isOwner = (teacherId) => {
  return authStore.user?.role === 'admin' || authStore.user?.id === teacherId;
};

const onSearchChange = () => {
  clearTimeout(searchTimeout.value);
  searchTimeout.value = setTimeout(() => {
    testStore.updateFilters({ search: filters.value.search });
    fetchData();
  }, 500);
};

const onFilterChange = () => {
  testStore.updateFilters(filters.value);
  fetchData();
};

const fetchData = async () => {
  await testStore.fetchAllTests(filters.value);
};

const goToPage = (page) => {
  testStore.updateFilters({
    ...filters.value,
    currentPage: page
  });
  fetchData();
};

const onDeleteTest = async (testId) => {
  if (confirm("Bạn chắc chắn muốn xóa bài test này?")) {
    try {
      await testStore.deleteTest(testId);
      alert("Xóa bài test thành công!");
    } catch (err) {
      alert("Lỗi: " + testStore.error);
    }
  }
};

const refreshData = async () => {
  await fetchData();
};

const exportData = () => {
  if (tests.value.length === 0) {
    alert("Không có dữ liệu để xuất");
    return;
  }

  // Prepare CSV data
  const headers = ['ID', 'Tên Bài Test', 'Giáo Viên', 'Thời Gian (phút)', 'Điểm Tối Đa', 'Trạng Thái', 'Ngày Tạo'];
  const rows = tests.value.map(test => [
    test.id,
    test.ten_bai_test,
    test.giao_vien?.name || 'Unknown',
    test.thoi_gian_toi_da,
    test.diem_tong_max,
    test.trang_thai === 2 ? 'Công Bố' : 'Nháp',
    new Date(test.created_at).toLocaleDateString('vi-VN')
  ]);

  // Create CSV content
  const csvContent = [
    headers.join(','),
    ...rows.map(row => row.map(cell => `"${cell}"`).join(','))
  ].join('\n');

  // Create and download file
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  const url = URL.createObjectURL(blob);
  link.setAttribute('href', url);
  link.setAttribute('download', `bai-test-${new Date().getTime()}.csv`);
  link.style.visibility = 'hidden';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

const updatePageSize = () => {
  testStore.updateFilters({
    ...filters.value,
    perPage: pageSize.value,
    currentPage: 1
  });
  fetchData();
};

// Lifecycle
onMounted(() => {
  fetchData();
});
</script>

<style scoped>
.hover-card {
  transition: transform 0.2s, box-shadow 0.2s;
}

.hover-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15) !important;
}

.card-header {
  border-bottom: 2px solid rgba(255, 255, 255, 0.2);
}

.text-truncate {
  max-width: 100%;
}

/* Button Group Styles */
.btn-group-sm .btn {
  padding: 0.25rem 0.5rem;
  font-size: 0.85rem;
}

/* Table Responsive */
.table-responsive {
  border-radius: 0.5rem;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.table {
  margin-bottom: 0;
}

.table thead th {
  font-weight: 600;
  padding: 0.75rem;
  vertical-align: middle;
}

.table tbody td {
  padding: 0.75rem;
  vertical-align: middle;
}

.table tbody tr:hover {
  background-color: #f8f9fa;
}

/* Settings Panel */
.btn-close {
  padding: 0;
  background-color: transparent;
  border: 0;
  opacity: 0.7;
}

.btn-close:hover {
  opacity: 1;
}

/* View Mode Toggle */
.btn-group .btn {
  margin: 0 2px;
}

/* Spinner Animation */
.fa-spin {
  animation: fa-spin 2s infinite linear;
}

@keyframes fa-spin {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .d-flex.flex-wrap gap-2 {
    flex-direction: column;
  }

  .btn,
  .btn-group {
    width: 100%;
  }

  .table {
    font-size: 0.9rem;
  }

  .table thead th,
  .table tbody td {
    padding: 0.5rem;
  }
}
</style>
