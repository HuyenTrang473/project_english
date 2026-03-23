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
      <!-- Quiz Type Section: Listening -->
      <div class="col-12">
        <h5 class="text-white mb-3">🎧 Quiz Nghe (Listening)</h5>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-lg hover-card cursor-pointer" @click="goToBuilder('listening_basic')">
          <div class="card-body text-center py-5">
            <div class="display-1 mb-3">🎧</div>
            <h5 class="card-title">Listening Cơ Bản</h5>
            <p class="card-text text-muted small">Quiz nghe đơn giản, 10-15 câu</p>
            <div class="mt-3">
              <span class="badge bg-info">20 phút</span>
              <span class="badge bg-success ms-1">100 điểm</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-lg hover-card cursor-pointer" @click="goToBuilder('listening_advanced')">
          <div class="card-body text-center py-5">
            <div class="display-1 mb-3">🎧📈</div>
            <h5 class="card-title">Listening Nâng Cao</h5>
            <p class="card-text text-muted small">Quiz nghe phức tạp, 20-25 câu</p>
            <div class="mt-3">
              <span class="badge bg-warning">45 phút</span>
              <span class="badge bg-danger ms-1">200 điểm</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Quiz Type Section: Reading -->
      <div class="col-12 mt-4">
        <h5 class="text-white mb-3">📖 Quiz Đọc (Reading)</h5>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-lg hover-card cursor-pointer" @click="goToBuilder('reading_basic')">
          <div class="card-body text-center py-5">
            <div class="display-1 mb-3">📖</div>
            <h5 class="card-title">Reading Cơ Bản</h5>
            <p class="card-text text-muted small">Quiz đọc hiểu đơn giản, 10-15 câu</p>
            <div class="mt-3">
              <span class="badge bg-info">25 phút</span>
              <span class="badge bg-success ms-1">100 điểm</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-lg hover-card cursor-pointer" @click="goToBuilder('reading_advanced')">
          <div class="card-body text-center py-5">
            <div class="display-1 mb-3">📖📈</div>
            <h5 class="card-title">Reading Nâng Cao</h5>
            <p class="card-text text-muted small">Quiz đọc hiểu phức tạp, 20-25 câu</p>
            <div class="mt-3">
              <span class="badge bg-warning">50 phút</span>
              <span class="badge bg-danger ms-1">200 điểm</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Quiz Type Section: Writing -->
      <div class="col-12 mt-4">
        <h5 class="text-white mb-3">✍️ Quiz Viết (Writing)</h5>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-lg hover-card cursor-pointer" @click="goToBuilder('writing_basic')">
          <div class="card-body text-center py-5">
            <div class="display-1 mb-3">✍️</div>
            <h5 class="card-title">Writing Cơ Bản</h5>
            <p class="card-text text-muted small">Quiz viết đơn giản, 5-8 câu</p>
            <div class="mt-3">
              <span class="badge bg-info">30 phút</span>
              <span class="badge bg-success ms-1">100 điểm</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-lg hover-card cursor-pointer" @click="goToBuilder('writing_advanced')">
          <div class="card-body text-center py-5">
            <div class="display-1 mb-3">✍️📈</div>
            <h5 class="card-title">Writing Nâng Cao</h5>
            <p class="card-text text-muted small">Quiz viết phức tạp, 8-12 câu</p>
            <div class="mt-3">
              <span class="badge bg-warning">60 phút</span>
              <span class="badge bg-danger ms-1">200 điểm</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Quiz Type Section: Mixed -->
      <div class="col-12 mt-4">
        <h5 class="text-white mb-3">🎯 Test Năng Lực Hỗn Hợp (Mixed)</h5>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-lg hover-card cursor-pointer" @click="goToBuilder('mixed_standard')">
          <div class="card-body text-center py-5">
            <div class="display-1 mb-3">🎯</div>
            <h5 class="card-title">Test Hỗn Hợp Chuẩn</h5>
            <p class="card-text text-muted small">Test tổng hợp tất cả kỹ năng</p>
            <div class="mt-3">
              <span class="badge bg-warning">90 phút</span>
              <span class="badge bg-danger ms-1">300 điểm</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-lg hover-card cursor-pointer" @click="goToBuilder('mixed_comprehensive')">
          <div class="card-body text-center py-5">
            <div class="display-1 mb-3">🎯🏆</div>
            <h5 class="card-title">Test Năng Lực Toàn Diện</h5>
            <p class="card-text text-muted small">Đánh giá chi tiết tất cả kỹ năng</p>
            <div class="mt-3">
              <span class="badge bg-warning">120 phút</span>
              <span class="badge bg-danger ms-1">500 điểm</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Other Templates -->
      <div class="col-12 mt-4">
        <h5 class="text-white mb-3">📋 Template Khác</h5>
      </div>
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
  listening_basic: 'Listening Cơ Bản',
  listening_advanced: 'Listening Nâng Cao',
  reading_basic: 'Reading Cơ Bản',
  reading_advanced: 'Reading Nâng Cao',
  writing_basic: 'Writing Cơ Bản',
  writing_advanced: 'Writing Nâng Cao',
  mixed_standard: 'Test Hỗn Hợp Chuẩn',
  mixed_comprehensive: 'Test Năng Lực Toàn Diện',
};

const templatePresets = {
  blank: {
    ten_bai_test: 'Bài Quiz Mới',
    loai_quiz: 'mixed',
    chi_tiet_loai_quiz: '',
    mo_ta: 'Bài quiz tùy chỉnh',
    thoi_gian_toi_da: 30,
    diem_tong_max: 100,
    so_lan_lam_toi_da: 3,
    diem_dat: 60,
  },
  quick10: {
    ten_bai_test: 'Quiz Nhanh - 10 Câu',
    loai_quiz: 'mixed',
    chi_tiet_loai_quiz: 'Quiz nhanh 10 câu để kiểm tra kiến thức cơ bản',
    mo_ta: 'Bài quiz nhanh 10 câu, thời gian 15 phút',
    thoi_gian_toi_da: 15,
    diem_tong_max: 100,
    so_lan_lam_toi_da: 5,
    diem_dat: 60,
  },
  standard: {
    ten_bai_test: 'Bài Test Chuẩn - 20 Câu',
    loai_quiz: 'mixed',
    chi_tiet_loai_quiz: 'Bài test chuẩn 20 câu để đánh giá chi tiết',
    mo_ta: 'Bài test tiêu chuẩn với 20 câu hỏi',
    thoi_gian_toi_da: 45,
    diem_tong_max: 200,
    so_lan_lam_toi_da: 3,
    diem_dat: 120,
  },
  midterm: {
    ten_bai_test: 'Bài Kiểm Tra Giữa Kỳ',
    loai_quiz: 'mixed',
    chi_tiet_loai_quiz: 'Bài kiểm tra toàn diện 30 câu đánh giá năng lực',
    mo_ta: 'Bài kiểm tra toàn diện 30 câu',
    thoi_gian_toi_da: 90,
    diem_tong_max: 300,
    so_lan_lam_toi_da: 1,
    diem_dat: 180,
  },
  essay: {
    ten_bai_test: 'Bài Tự Luận',
    loai_quiz: 'writing',
    chi_tiet_loai_quiz: 'Bài viết tự luận để đánh giá kỹ năng viết',
    mo_ta: 'Bài test với câu hỏi tự luận',
    thoi_gian_toi_da: 60,
    diem_tong_max: 150,
    so_lan_lam_toi_da: 2,
    diem_dat: 90,
  },
  mixed: {
    ten_bai_test: 'Test Hỗn Hợp',
    loai_quiz: 'mixed',
    chi_tiet_loai_quiz: 'Kết hợp nhiều dạng câu hỏi và kỹ năng khác nhau',
    mo_ta: 'Bài test kết hợp nhiều dạng câu hỏi',
    thoi_gian_toi_da: 60,
    diem_tong_max: 250,
    so_lan_lam_toi_da: 2,
    diem_dat: 150,
  },
  // Listening Templates
  listening_basic: {
    ten_bai_test: 'Listening Cơ Bản',
    loai_quiz: 'listening',
    chi_tiet_loai_quiz: 'Bài nghe đơn giản, 10-15 câu, tập trung vào từ vựng và ngữ pháp cơ bản',
    mo_ta: 'Quiz nghe hiểu cơ bản',
    thoi_gian_toi_da: 20,
    diem_tong_max: 100,
    so_lan_lam_toi_da: 3,
    diem_dat: 60,
  },
  listening_advanced: {
    ten_bai_test: 'Listening Nâng Cao',
    loai_quiz: 'listening',
    chi_tiet_loai_quiz: 'Bài nghe phức tạp, 20-25 câu, đánh giá khả năng nghe hiểu sâu',
    mo_ta: 'Quiz nghe hiểu nâng cao',
    thoi_gian_toi_da: 45,
    diem_tong_max: 200,
    so_lan_lam_toi_da: 2,
    diem_dat: 120,
  },
  // Reading Templates
  reading_basic: {
    ten_bai_test: 'Reading Cơ Bản',
    loai_quiz: 'reading',
    chi_tiet_loai_quiz: 'Bài đọc đơn giản, 10-15 câu, tập trung vào hiểu ý chính',
    mo_ta: 'Quiz đọc hiểu cơ bản',
    thoi_gian_toi_da: 25,
    diem_tong_max: 100,
    so_lan_lam_toi_da: 3,
    diem_dat: 60,
  },
  reading_advanced: {
    ten_bai_test: 'Reading Nâng Cao',
    loai_quiz: 'reading',
    chi_tiet_loai_quiz: 'Bài đọc phức tạp, 20-25 câu, đánh giá khả năng đọc hiểu chi tiết',
    mo_ta: 'Quiz đọc hiểu nâng cao',
    thoi_gian_toi_da: 50,
    diem_tong_max: 200,
    so_lan_lam_toi_da: 2,
    diem_dat: 120,
  },
  // Writing Templates
  writing_basic: {
    ten_bai_test: 'Writing Cơ Bản',
    loai_quiz: 'writing',
    chi_tiet_loai_quiz: 'Bài viết đơn giản, 5-8 câu, tập trung vào câu và biểu đạt cơ bản',
    mo_ta: 'Quiz viết cơ bản',
    thoi_gian_toi_da: 30,
    diem_tong_max: 100,
    so_lan_lam_toi_da: 3,
    diem_dat: 60,
  },
  writing_advanced: {
    ten_bai_test: 'Writing Nâng Cao',
    loai_quiz: 'writing',
    chi_tiet_loai_quiz: 'Bài viết phức tạp, 8-12 câu, đánh giá kỹ năng viết chi tiết',
    mo_ta: 'Quiz viết nâng cao',
    thoi_gian_toi_da: 60,
    diem_tong_max: 200,
    so_lan_lam_toi_da: 2,
    diem_dat: 120,
  },
  // Mixed/Comprehensive Templates
  mixed_standard: {
    ten_bai_test: 'Test Hỗn Hợp Chuẩn',
    loai_quiz: 'mixed',
    chi_tiet_loai_quiz: 'Test năng lực hỗn hợp chuẩn, kết hợp nghe, đọc, viết',
    mo_ta: 'Test tổng hợp tất cả kỹ năng',
    thoi_gian_toi_da: 90,
    diem_tong_max: 300,
    so_lan_lam_toi_da: 2,
    diem_dat: 180,
  },
  mixed_comprehensive: {
    ten_bai_test: 'Test Năng Lực Toàn Diện',
    loai_quiz: 'mixed',
    chi_tiet_loai_quiz: 'Test đánh giá toàn diện, chi tiết tất cả các kỹ năng tiếng Anh',
    mo_ta: 'Đánh giá chi tiết tất cả kỹ năng',
    thoi_gian_toi_da: 120,
    diem_tong_max: 500,
    so_lan_lam_toi_da: 1,
    diem_dat: 300,
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
