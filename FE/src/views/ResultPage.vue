<template>
  <div class="container-fluid py-4">
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Đang tải...</span>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="alert alert-danger text-center">
      <h4>⚠️ {{ error }}</h4>
      <router-link to="/" class="btn btn-primary mt-3">Về trang chủ</router-link>
    </div>

    <!-- Results Display -->
    <div v-else>
      <!-- Header -->
      <div class="text-center mb-5">
        <h1>📊 Kết Quả Bài Test</h1>
      </div>

      <!-- Score Card -->
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="card text-center">
            <div class="card-body py-5">
              <h2 class="display-1" :style="{ color: scoreColor }">
                {{ result.diem_tong }}
              </h2>
              <h5 class="text-muted mb-3">Điểm Của Bạn</h5>
              <div class="progress mb-3" style="height: 25px;">
                <div 
                  class="progress-bar"
                  :style="{ 
                    width: scorePercentage + '%',
                    backgroundColor: scoreColor
                  }"
                >
                  {{ scorePercentage }}%
                </div>
              </div>
              <p class="mb-0">
                <span 
                  :class="[
                    'badge badge-lg',
                    isPassed ? 'bg-success' : 'bg-danger'
                  ]"
                >
                  {{ isPassed ? '✓ ĐẠT' : '✗ KHÔNG ĐẠT' }}
                </span>
              </p>
            </div>
          </div>
        </div>

        <!-- Score Details -->
        <div class="col-md-6">
          <div class="card">
            <div class="card-header bg-info text-white">
              <h5 class="mb-0">📈 Chi Tiết</h5>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <p class="mb-1"><strong>Lần Làm:</strong> Lần #{{ result.lan_thu }}</p>
                <p class="mb-1"><strong>Thời Gian Làm:</strong> {{ formattedDuration }}</p>
                <p class="mb-1"><strong>Ngày Nộp:</strong> {{ formatDate(result.thoi_gian_hoan_thanh) }}</p>
              </div>
              <div class="border-top pt-3">
                <p class="mb-1">
                  <span class="badge bg-success me-2">✓ Đúng: {{ result.so_cau_dung }}</span>
                  <span class="badge bg-danger me-2">✗ Sai: {{ result.so_cau_sai }}</span>
                  <span class="badge bg-secondary">⊘ Bỏ trống: {{ result.so_cau_bo_trong }}</span>
                </p>
                <p class="mb-0 mt-2">
                  <strong>Trạng Thái:</strong>
                  <span 
                    :class="[
                      'badge',
                      result.trang_thai === 'completed' ? 'bg-success' : 'bg-warning'
                    ]"
                  >
                    {{ statusText }}
                  </span>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Questions Review -->
      <div v-if="chiTietTungCau.length" class="card mb-4">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">❓ Chi Tiết Từng Câu</h5>
        </div>
        <div class="card-body">
          <div 
            v-for="(item, index) in chiTietTungCau"
            :key="index"
            class="mb-4 pb-4 border-bottom"
          >
            <!-- Question -->
            <div class="mb-3">
              <h6 class="mb-2">
                <span class="badge bg-secondary">Câu {{ index + 1 }}</span>
                <span class="ms-2">{{ item.noi_dung_cau_hoi }}</span>
              </h6>
            </div>

            <!-- Student Answer -->
            <div class="mb-2">
              <p class="mb-1">
                <strong>📝 Câu Trả Lời Của Bạn:</strong>
              </p>
              <div 
                class="alert py-2"
                :class="{
                  'alert-success': item.diem_tong > 0,
                  'alert-danger': item.diem_tong <= 0
                }"
              >
                {{ item.dap_an_chon || 'Không có câu trả lời' }}
              </div>
            </div>

            <!-- Correct Answer -->
            <div v-if="item.dap_an_dung" class="mb-2">
              <p class="mb-1">
                <strong>✅ Đáp Án Đúng:</strong>
              </p>
              <div class="alert alert-success py-2">
                {{ item.dap_an_dung }}
              </div>
            </div>

            <!-- Score -->
            <div class="mb-0">
              <span class="badge bg-info">
                {{ item.diem_tong }} / {{ item.diem_max }} điểm
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="d-flex gap-2 mb-4">
        <!-- Back Button -->
        <router-link 
          to="/"
          class="btn btn-outline-secondary"
        >
          <i class="fa fa-arrow-left"></i> Quay Lại Trang Chủ
        </router-link>

        <!-- Print Button -->
        <button 
          @click="onPrint"
          class="btn btn-outline-info"
        >
          <i class="fa fa-print"></i> In Kết Quả
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useTestStore } from "@/stores/test";

const route = useRoute();
const router = useRouter();
const testStore = useTestStore();

// State
const testId = ref(route.params.id);
const result = ref({});
const chiTietTungCau = ref([]);
const loading = ref(true);
const error = ref("");

// Computed
const scorePercentage = computed(() => {
  if (!result.value.diem_tong && result.value.diem_tong !== 0) return 0;
  const maxScore = result.value.diem_tong_max || 
    chiTietTungCau.value.reduce((sum, q) => sum + (q.diem_max || 0), 0) || 100;
  return Math.round((result.value.diem_tong / maxScore) * 100);
});

const scoreColor = computed(() => {
  const percentage = scorePercentage.value;
  if (percentage >= 80) return "#28a745";
  if (percentage >= 60) return "#ffc107";
  return "#dc3545";
});

const isPassed = computed(() => {
  return scorePercentage.value >= 50;
});

const statusText = computed(() => {
  const status = {
    not_started: "Chưa làm",
    in_progress: "Đang làm",
    completed: "Hoàn thành",
    pending_review: "Chờ chấm điểm",
    grading: "Đang chấm",
  };
  return status[result.value.trang_thai] || result.value.trang_thai;
});

const formattedDuration = computed(() => {
  const seconds = result.value.thoi_gian_su_dung || 0;
  const minutes = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${minutes} phút ${secs} giây`;
});

// Methods
const formatDate = (date) => {
  if (!date) return "";
  return new Date(date).toLocaleString("vi-VN");
};

const onPrint = () => {
  window.print();
};

// Load result
const loadResult = async () => {
  loading.value = true;
  error.value = "";
  try {
    await testStore.fetchResult(testId.value);
    const data = testStore.testResult;
    
    if (!data) {
      error.value = "Không tìm thấy kết quả bài test";
      return;
    }

    result.value = data;
    chiTietTungCau.value = data.chi_tiet_tung_cau || [];
  } catch (err) {
    error.value = testStore.error || "Lỗi tải kết quả";
  } finally {
    loading.value = false;
  }
};

// Lifecycle
onMounted(() => {
  loadResult();
});
</script>

<style scoped>
.display-1 {
  font-weight: bold;
}

.badge-lg {
  font-size: 1rem;
  padding: 0.5rem 1rem;
}

.progress {
  border-radius: 10px;
}

.alert {
  border-left: 4px solid;
}

.alert-success {
  border-left-color: #28a745;
  background-color: #d4edda;
}

.alert-danger {
  border-left-color: #dc3545;
  background-color: #f8d7da;
}
</style>
