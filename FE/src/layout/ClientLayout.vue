<template>
    <div class="client-layout d-flex flex-column min-vh-100">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <router-link class="navbar-brand fw-bold text-primary" to="/">EnglishHub</router-link>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                    aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <router-link class="nav-link" to="/">Trang chủ</router-link>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="testDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Bài Kiểm Tra
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="testDropdown">
                                <li v-for="test in availableTests" :key="test.testId">
                                    <router-link class="dropdown-item" :to="`/tests/${test.testId}/take`">
                                        {{ test.testName }}
                                    </router-link>
                                </li>
                                <li v-if="availableTests.length === 0">
                                    <span class="dropdown-item text-muted">Chưa có bài test</span>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <div class="d-flex gap-2">
                        <template v-if="authStore.isAuthenticated">
                            <span class="nav-link text-muted">{{ authStore.user?.name }}</span>
                            <button class="btn btn-outline-danger btn-sm" @click="handleLogout" :disabled="isLoggingOut">
                              <span v-if="isLoggingOut" class="spinner-border spinner-border-sm me-2"></span>
                              {{ isLoggingOut ? "Đang xuất..." : "Đăng xuất" }}
                            </button>
                        </template>
                        <template v-else>
                            <router-link class="btn btn-outline-secondary" to="/login">Đăng nhập</router-link>
                            <router-link class="btn btn-primary" to="/register">Đăng ký</router-link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>
        <main class="flex-grow-1">
            <router-view></router-view>
        </main>
        <footer class="bg-light py-4 border-top">
            <div class="container text-center text-muted">
                <small>&copy; 2024 EnglishHub. All rights reserved.</small>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { getLessons } from '@/api/lessonApi';
import { getListByLesson } from '@/api/testApi';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const availableTests = ref([]);
const isLoggingOut = ref(false);

const handleLogout = async () => {
  isLoggingOut.value = true;
  try {
    const result = await authStore.logout();
    if (result.success) {
      await router.push({ name: "Login" });
    } else {
      alert(result.message || "Đăng xuất thất bại");
    }
  } catch (error) {
    console.error("Logout error:", error);
    alert("Đăng xuất bị lỗi");
  } finally {
    isLoggingOut.value = false;
  }
};

const loadAvailableTests = async () => {
    try {
        const lessonRes = await getLessons();
        const lessons = lessonRes.data || [];
        for (const lesson of lessons) {
            const testRes = await getListByLesson(lesson.id);
            const tests = testRes.data || [];
            if (tests.length > 0) {
                for (const test of tests) {
                    const testName = test.ten_bai_test || 'Bài test';
                    availableTests.value.push({
                        testId: test.id,
                        testName: testName
                    });
                }
            }
        }
    } catch (error) {
        console.error('Failed to load available tests', error);
    }
};

onMounted(() => {
    loadAvailableTests();
});

// Removed goToTestPage logic because links are now rendered directly via router-link
</script>

<style scoped>
.client-layout {
    background-color: #f8f9fa;
}
</style>
