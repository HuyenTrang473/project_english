<template>
  <div class="container py-5" style="min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <!-- Header -->
    <div class="text-center mb-5">
      <h1 class="text-white mb-3">
        <i class="fas fa-graduation-cap"></i> Tạo Bài Quiz Mới
      </h1>
      <p class="text-white-50">Chọn một template để bắt đầu hoặc bắt đầu từ đầu</p>
    </div>

    <!-- Templates Grid -->
    <div class="row g-4">
      <!-- Blank Quiz -->
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-lg hover-card cursor-pointer" @click="goToBuilder('blank')">
          <div class="card-body text-center py-5">
            <div class="display-1 mb-3">📝</div>
            <h5 class="card-title">Bài Quiz Trắng</h5>
            <p class="card-text text-muted small">Tạo từ đầu với các cài đặt tùy chỉnh</p>
            <div class="mt-3">
              <span class="badge bg-light text-dark">Tự do</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick 10 Questions -->
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-lg hover-card cursor-pointer" @click="goToBuilder('quick10')">
          <div class="card-body text-center py-5">
            <div class="display-1 mb-3">⚡</div>
            <h5 class="card-title">Quiz Nhanh (10 câu)</h5>
            <p class="card-text text-muted small">Bài test 10 câu với cấu hình mặc định</p>
            <div class="mt-3">
              <span class="badge bg-info">15 phút</span>
              <span class="badge bg-success ms-1">100 điểm</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Standard Test -->
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-lg hover-card cursor-pointer" @click="goToBuilder('standard')">
          <div class="card-body text-center py-5">
            <div class="display-1 mb-3">📋</div>
            <h5 class="card-title">Bài Test Chuẩn (20 câu)</h5>
            <p class="card-text text-muted small">Bài test tiêu chuẩn 20 câu chi tiết</p>
            <div class="mt-3">
              <span class="badge bg-warning">45 phút</span>
              <span class="badge bg-danger ms-1">200 điểm</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Midterm Exam -->
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-lg hover-card cursor-pointer" @click="goToBuilder('midterm')">
          <div class="card-body text-center py-5">
            <div class="display-1 mb-3">🏆</div>
            <h5 class="card-title">Bài Kiểm Tra Giữa Kỳ</h5>
            <p class="card-text text-muted small">Bài test hoàn chỉnh 30 câu với đánh giá toàn diện</p>
            <div class="mt-3">
              <span class="badge bg-warning">90 phút</span>
              <span class="badge bg-danger ms-1">300 điểm</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Speaking/Essay Test -->
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-lg hover-card cursor-pointer" @click="goToBuilder('essay')">
          <div class="card-body text-center py-5">
            <div class="display-1 mb-3">✒️</div>
            <h5 class="card-title">Bài Tự Luận</h5>
            <p class="card-text text-muted small">Bài test với câu hỏi tự luận và essay</p>
            <div class="mt-3">
              <span class="badge bg-primary">60 phút</span>
              <span class="badge bg-success ms-1">150 điểm</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Mixed Type Test -->
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-lg hover-card cursor-pointer" @click="goToBuilder('mixed')">
          <div class="card-body text-center py-5">
            <div class="display-1 mb-3">🎯</div>
            <h5 class="card-title">Test Hỗn Hợp</h5>
            <p class="card-text text-muted small">Kết hợp nhiều dạng câu hỏi khác nhau</p>
            <div class="mt-3">
              <span class="badge bg-warning">60 phút</span>
              <span class="badge bg-danger ms-1">250 điểm</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Tests -->
    <div v-if="recentTests.length > 0" class="mt-5">
      <h5 class="text-white mb-3">📚 Bài Test Gần Đây</h5>
      <div class="row g-3">
        <div 
          v-for="test in recentTests"
          :key="test.id"
          class="col-md-6"
        >
          <div 
            class="card bg-white text-dark hover-card cursor-pointer"
            @click="goToEdit(test.id)"
          >
            <div class="card-body">
              <h6 class="card-title mb-1">{{ test.ten_bai_test }}</h6>
              <small class="text-muted">{{ test.questions?.length || 0 }} câu hỏi • {{ test.thoi_gian_toi_da }} phút</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="text-center mt-5">
      <router-link to="/tests" class="btn btn-outline-light">
        <i class="fa fa-arrow-left"></i> Quay Lại Danh Sách
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useTestStore } from '@/stores/test';

const router = useRouter();
const testStore = useTestStore();
const recentTests = ref([]);

const templateNames = {
  blank: 'Bài Quiz Trắng',
  quick10: 'Quiz Nhanh (10 câu)',
  standard: 'Bài Test Chuẩn (20 câu)',
  midterm: 'Bài Kiểm Tra Giữa Kỳ',
  essay: 'Bài Tự Luận',
  mixed: 'Bài Test Hỗn Hợp',
};

const templatePresets = {
  blank: {
    ten_bai_test: 'Bài Quiz Mới',
    mo_ta: 'Bài quiz tùy chỉnh',
    thoi_gian_toi_da: 30,
    diem_tong_max: 100,
    so_lan_lam_toi_da: 3,
    diem_dat: 60,
    tro_luc_cau_hoi: true,
    tro_luc_dap_an: true,
    hien_thi_ket_qua_ngay: true,
    hien_thi_dap_an_dung: true,
  },
  quick10: {
    ten_bai_test: 'Quiz Nhanh - 10 Câu',
    mo_ta: 'Bài quiz nhanh 10 câu, thời gian 15 phút',
    thoi_gian_toi_da: 15,
    diem_tong_max: 100,
    so_lan_lam_toi_da: 5,
    diem_dat: 60,
    tro_luc_cau_hoi: true,
    tro_luc_dap_an: true,
    hien_thi_ket_qua_ngay: true,
    hien_thi_dap_an_dung: true,
  },
  standard: {
    ten_bai_test: 'Bài Test Chuẩn - 20 Câu',
    mo_ta: 'Bài test tiêu chuẩn với 20 câu hỏi',
    thoi_gian_toi_da: 45,
    diem_tong_max: 200,
    so_lan_lam_toi_da: 3,
    diem_dat: 120,
    tro_luc_cau_hoi: true,
    tro_luc_dap_an: true,
    hien_thi_ket_qua_ngay: true,
    hien_thi_dap_an_dung: true,
  },
  midterm: {
    ten_bai_test: 'Bài Kiểm Tra Giữa Kỳ',
    mo_ta: 'Bài kiểm tra toàn diện 30 câu',
    thoi_gian_toi_da: 90,
    diem_tong_max: 300,
    so_lan_lam_toi_da: 1,
    diem_dat: 180,
    tro_luc_cau_hoi: true,
    tro_luc_dap_an: false,
    hien_thi_ket_qua_ngay: false,
    hien_thi_dap_an_dung: false,
  },
  essay: {
    ten_bai_test: 'Bài Tự Luận',
    mo_ta: 'Bài test với câu hỏi tự luận',
    thoi_gian_toi_da: 60,
    diem_tong_max: 150,
    so_lan_lam_toi_da: 2,
    diem_dat: 90,
    tro_luc_cau_hoi: false,
    tro_luc_dap_an: false,
    hien_thi_ket_qua_ngay: false,
    hien_thi_dap_an_dung: false,
  },
  mixed: {
    ten_bai_test: 'Test Hỗn Hợp',
    mo_ta: 'Bài test kết hợp nhiều dạng câu hỏi',
    thoi_gian_toi_da: 60,
    diem_tong_max: 250,
    so_lan_lam_toi_da: 2,
    diem_dat: 150,
    tro_luc_cau_hoi: true,
    tro_luc_dap_an: true,
    hien_thi_ket_qua_ngay: true,
    hien_thi_dap_an_dung: true,
  },
};

const goToBuilder = (templateKey) => {
  const preset = templatePresets[templateKey];
  const templateName = templateNames[templateKey];
  // Store preset and template name in sessionStorage to pass to builder
  sessionStorage.setItem('testTemplatePreset', JSON.stringify(preset));
  sessionStorage.setItem('testTemplateName', templateName);
  router.push('/tests/create');
};

const goToEdit = (testId) => {
  router.push(`/tests/${testId}/edit`);
};

const loadRecentTests = async () => {
  try {
    // Use store to fetch recent tests
    await testStore.fetchMyTests();
    recentTests.value = testStore.tests.slice(0, 3);
  } catch (err) {
    console.log('Could not load recent tests');
  }
};

onMounted(() => {
  loadRecentTests();
});
</script>

<style scoped>
.hover-card {
  transition: all 0.3s ease;
  border: none;
}

.hover-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2) !important;
}

.cursor-pointer {
  cursor: pointer;
}

.text-white-50 {
  color: rgba(255, 255, 255, 0.7);
}

.bg-light.text-dark {
  background-color: rgba(255, 255, 255, 0.95) !important;
}
</style>
