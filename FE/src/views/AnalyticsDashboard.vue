<template>
  <div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>📊 Thống Kê Bài Test</h1>
      <router-link 
        to="/tests"
        class="btn btn-outline-secondary"
      >
        <i class="fa fa-arrow-left"></i> Quay Lại
      </router-link>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Đang tải...</span>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else>
      <!-- Test Info Card -->
      <div class="card mb-4">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">📝 Thông Tin Bài Test</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <p><strong>Tên:</strong> {{ test.ten_bai_test }}</p>
              <p><strong>Mô Tả:</strong> {{ test.mo_ta }}</p>
              <p><strong>Thời Gian Tối Đa:</strong> {{ test.thoi_gian_toi_da }} phút</p>
            </div>
            <div class="col-md-6">
              <p><strong>Điểm Tối Đa:</strong> {{ test.diem_tong_max }}</p>
              <p><strong>Điểm Đạt:</strong> {{ test.diem_dat }}</p>
              <p><strong>Số Câu Hỏi:</strong> {{ questions.length }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="card text-center">
            <div class="card-body">
              <h2 class="text-primary">{{ totalStudents }}</h2>
              <p class="text-muted mb-0">Học Sinh Làm Bài</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-center">
            <div class="card-body">
              <h2 class="text-success">{{ averageScore.toFixed(1) }}</h2>
              <p class="text-muted mb-0">Điểm Trung Bình</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-center">
            <div class="card-body">
              <h2 class="text-info">{{ passRate.toFixed(1) }}%</h2>
              <p class="text-muted mb-0">Tỷ Lệ Đạt</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-center">
            <div class="card-body">
              <h2 class="text-warning">{{ averageTime.toFixed(1) }}</h2>
              <p class="text-muted mb-0">Thời Gian TB (phút)</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="row mb-4">
        <!-- Score Distribution Chart -->
        <div class="col-md-6">
          <div class="card">
            <div class="card-header bg-success text-white">
              <h5 class="mb-0">📈 Phân Bố Điểm</h5>
            </div>
            <div class="card-body">
              <canvas 
                v-if="chartInstance"
                id="scoreChart"
                style="max-height: 300px"
              ></canvas>
              <p v-else class="text-muted text-center">Đang tải biểu đồ...</p>
            </div>
          </div>
        </div>

        <!-- Pass/Fail Distribution -->
        <div class="col-md-6">
          <div class="card">
            <div class="card-header bg-info text-white">
              <h5 class="mb-0">📊 Tỷ Lệ Đạt/Không Đạt</h5>
            </div>
            <div class="card-body">
              <div class="mx-auto" style="max-width: 300px;">
                <canvas 
                  v-if="pieChartInstance"
                  id="passRateChart"
                ></canvas>
              </div>
              <div class="mt-3 text-center">
                <span class="badge bg-success me-2">✓ Đạt: {{ passedCount }}</span>
                <span class="badge bg-danger">✗ Không Đạt: {{ failedCount }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Question Analytics -->
      <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
          <h5 class="mb-0">❓ Phân Tích Chi Tiết Câu Hỏi</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped">
              <thead class="table-light">
                <tr>
                  <th>STT</th>
                  <th>Câu Hỏi</th>
                  <th>Loại</th>
                  <th>Địa Điểm</th>
                  <th>Tỷ Lệ Đúng %</th>
                  <th>Điểm TB</th>
                  <th>Độ Khó</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(question, index) in questions" :key="question.id">
                  <td>{{ index + 1 }}</td>
                  <td>
                    <span class="text-truncate" style="max-width: 200px;">
                      {{ question.noi_dung }}
                    </span>
                  </td>
                  <td>
                    <span class="badge bg-secondary">{{ question.loai_cau_hoi }}</span>
                  </td>
                  <td>{{ question.diem_toi_da }}</td>
                  <td>
                    <div class="progress" style="height: 20px;">
                      <div 
                        class="progress-bar"
                        :style="{ width: getQuestionCorrectRate(question) + '%' }"
                      >
                        {{ getQuestionCorrectRate(question).toFixed(1) }}%
                      </div>
                    </div>
                  </td>
                  <td>{{ getQuestionAverageScore(question).toFixed(2) }}</td>
                  <td>
                    <span 
                      :class="[
                        'badge',
                        getDifficulty(question) === 'Dễ' ? 'bg-success' : 
                        getDifficulty(question) === 'Trung Bình' ? 'bg-warning' : 'bg-danger'
                      ]"
                    >
                      {{ getDifficulty(question) }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Student Attempts -->
      <div class="card mb-4">
        <div class="card-header bg-dark text-white">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">👥 Kết Quả Học Sinh</h5>
            <div>
              <input 
                v-model="searchStudent"
                type="text"
                class="form-control form-control-sm"
                placeholder="Tìm học sinh..."
                style="width: 200px; display: inline-width"
              >
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>Học Sinh</th>
                  <th>Lần Làm</th>
                  <th>Điểm</th>
                  <th>Tỷ Lệ %</th>
                  <th>Thời Gian</th>
                  <th>Ngày Nộp</th>
                  <th>Trạng Thái</th>
                  <th>Hành Động</th>
                </tr>
              </thead>
              <tbody>
                <tr 
                  v-for="attempt in filteredAttempts"
                  :key="attempt.id"
                  :class="{
                    'table-success': attempt.diem_thi >= test.diem_dat,
                    'table-danger': attempt.diem_thi < test.diem_dat
                  }"
                >
                  <td>
                    <strong>{{ attempt.hoc_sinh_name }}</strong>
                    <br>
                    <small class="text-muted">{{ attempt.email }}</small>
                  </td>
                  <td>{{ attempt.lan_thu }}</td>
                  <td>
                    <h6 class="mb-0">{{ attempt.diem_thi }}</h6>
                    <small class="text-muted">/ {{ test.diem_tong_max }}</small>
                  </td>
                  <td>
                    {{ ((attempt.diem_thi / test.diem_tong_max) * 100).toFixed(1) }}%
                  </td>
                  <td>{{ attempt.thoi_gian_lam_tot }} phút</td>
                  <td>{{ formatDate(attempt.ngay_nop) }}</td>
                  <td>
                    <span 
                      :class="[
                        'badge',
                        attempt.trang_thai === 'graded' ? 'bg-success' :
                        attempt.trang_thai === 'pending_review' ? 'bg-warning' : 'bg-info'
                      ]"
                    >
                      {{ getStatusText(attempt.trang_thai) }}
                    </span>
                  </td>
                  <td>
                    <router-link 
                      :to="`/tests/${testId}/result/${attempt.id}`"
                      class="btn btn-sm btn-info"
                    >
                      <i class="fa fa-eye"></i>
                    </router-link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Export Button -->
      <div class="d-grid gap-2">
        <button 
          @click="onExportCSV"
          class="btn btn-lg btn-outline-success"
        >
          <i class="fa fa-download"></i> Xuất Excel CSV
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute } from "vue-router";
import { useTestStore } from "@/stores/test";
import Chart from "chart.js/auto";

const route = useRoute();
const testStore = useTestStore();

// State
const testId = ref(route.params.id);
const test = ref({});
const questions = ref([]);
const attempts = ref([]);
const chartInstance = ref(null);
const pieChartInstance = ref(null);
const searchStudent = ref("");

// Computed
const loading = computed(() => testStore.loading);

const totalStudents = computed(() => {
  return new Set(attempts.value.map(a => a.id_hoc_sinh)).size;
});

const averageScore = computed(() => {
  if (attempts.value.length === 0) return 0;
  return attempts.value.reduce((sum, a) => sum + a.diem_thi, 0) / attempts.value.length;
});

const passedCount = computed(() => {
  return attempts.value.filter(a => a.diem_thi >= test.value.diem_dat).length;
});

const failedCount = computed(() => {
  return attempts.value.filter(a => a.diem_thi < test.value.diem_dat).length;
});

const passRate = computed(() => {
  if (attempts.value.length === 0) return 0;
  return (passedCount.value / attempts.value.length) * 100;
});

const averageTime = computed(() => {
  if (attempts.value.length === 0) return 0;
  return attempts.value.reduce((sum, a) => sum + a.thoi_gian_lam_tot, 0) / attempts.value.length;
});

const filteredAttempts = computed(() => {
  if (!searchStudent.value) return attempts.value;
  return attempts.value.filter(a =>
    a.hoc_sinh_name.toLowerCase().includes(searchStudent.value.toLowerCase()) ||
    a.email.toLowerCase().includes(searchStudent.value.toLowerCase())
  );
});

// Methods
const formatDate = (date) => {
  if (!date) return "";
  return new Date(date).toLocaleString("vi-VN");
};

const getStatusText = (status) => {
  const statusMap = {
    not_started: "Chưa làm",
    in_progress: "Đang làm",
    completed: "Hoàn thành",
    pending_review: "Chờ chấm",
    graded: "Đã chấm",
  };
  return statusMap[status] || status;
};

const getQuestionCorrectRate = (question) => {
  const questionAnalytics = question.analytics || {};
  return questionAnalytics.percent_correct || 0;
};

const getQuestionAverageScore = (question) => {
  const questionAnalytics = question.analytics || {};
  return questionAnalytics.average_score || 0;
};

const getDifficulty = (question) => {
  const correctRate = getQuestionCorrectRate(question);
  if (correctRate >= 80) return "Dễ";
  if (correctRate >= 50) return "Trung Bình";
  return "Khó";
};

const initCharts = () => {
  // Score Distribution Chart
  const ctx = document.getElementById("scoreChart");
  if (ctx) {
    chartInstance.value = new Chart(ctx, {
      type: "bar",
      data: {
        labels: ["0-20", "20-40", "40-60", "60-80", "80-100"],
        datasets: [{
          label: "Số Học Sinh",
          data: getScoreDistribution(),
          backgroundColor: ["#dc3545", "#fd7e14", "#ffc107", "#17a2b8", "#28a745"],
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
      },
    });
  }

  // Pass/Fail Pie Chart
  const pieCtx = document.getElementById("passRateChart");
  if (pieCtx) {
    pieChartInstance.value = new Chart(pieCtx, {
      type: "pie",
      data: {
        labels: ["Đạt", "Không Đạt"],
        datasets: [{
          data: [passedCount.value, failedCount.value],
          backgroundColor: ["#28a745", "#dc3545"],
        }],
      },
      options: {
        responsive: true,
      },
    });
  }
};

const getScoreDistribution = () => {
  const ranges = [0, 20, 40, 60, 80, 100];
  return ranges.map((_, index) => {
    if (index === ranges.length - 1) return 0;
    return attempts.value.filter(a => {
      const score = (a.diem_thi / test.value.diem_tong_max) * 100;
      return score >= ranges[index] && score < ranges[index + 1];
    }).length;
  });
};

const onExportCSV = () => {
  const headers = ["Tên Học Sinh", "Email", "Lần Làm", "Điểm", "Thời Gian", "Ngày Nộp", "Trạng Thái"];
  const rows = filteredAttempts.value.map(a => [
    a.hoc_sinh_name,
    a.email,
    a.lan_thu,
    a.diem_thi,
    a.thoi_gian_lam_tot,
    formatDate(a.ngay_nop),
    getStatusText(a.trang_thai),
  ]);

  const csv = [headers, ...rows].map(row => row.join(",")).join("\n");
  const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = `analytics_${test.value.ten_bai_test}_${new Date().getTime()}.csv`;
  link.click();
};

// Load analytics
const loadAnalytics = async () => {
  try {
    const data = await testStore.fetchAnalytics(testId.value);
    test.value = data.test;
    questions.value = data.questions || [];
    attempts.value = data.attempts || [];
    
    // Initialize charts after data loads
    setTimeout(initCharts, 100);
  } catch (err) {
    alert("Lỗi tải thống kê: " + testStore.error);
  }
};

// Lifecycle
onMounted(() => {
  loadAnalytics();
});
</script>

<style scoped>
canvas {
  max-height: 300px;
}

.table-hover tbody tr:hover {
  background-color: rgba(0, 0, 0, 0.05);
}

.badge {
  font-size: 0.85rem;
}

.form-control-sm {
  font-size: 0.875rem;
}
</style>
