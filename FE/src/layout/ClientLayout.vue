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
                    <ul class="navbar-nav me-auto ms-lg-4 mb-2 mb-lg-0 align-items-center">
                        <li class="nav-item px-2">
                            <router-link class="nav-link fw-semibold" to="/">Trang chủ</router-link>
                        </li>
                        <li class="nav-item dropdown px-2 position-relative">
                            <a class="nav-link dropdown-toggle fw-semibold" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Danh sách khóa học
                            </a>
                            <ul class="dropdown-menu shadow-sm border-0 mt-2 dropdown-scrollable">
                                <li v-if="isLoadingData" class="text-center py-2 text-muted small">
                                    <span class="spinner-border spinner-border-sm me-2"></span> Đang tải...
                                </li>
                                <li v-else v-for="course in availableCourses" :key="course.id">
                                    <router-link class="dropdown-item py-2" :to="`/lessons/${course.id}`">{{
                                        course.title }}</router-link>
                                </li>
                                <li v-if="!isLoadingData && availableCourses.length === 0">
                                    <span class="dropdown-item py-2 text-muted small">Chưa có khóa học</span>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown px-2 position-relative">
                            <a class="nav-link dropdown-toggle fw-semibold" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Bài test
                            </a>
                            <ul class="dropdown-menu shadow-sm border-0 mt-2">
                                <li v-if="isLoadingData" class="text-center py-2 text-muted small">
                                    <span class="spinner-border spinner-border-sm me-2"></span> Đang tải...
                                </li>
                                <li v-else v-for="test in availableTests" :key="test.testId">
                                    <router-link class="dropdown-item py-2" :to="`/test/${test.testId}`">{{
                                        test.testName }}</router-link>
                                </li>
                                <li v-if="!isLoadingData && availableTests.length === 0">
                                    <span class="dropdown-item py-2 text-muted small">Chưa có bài test</span>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <div class="d-flex gap-2 ms-lg-4 mt-3 mt-lg-0">
                        <template v-if="authStore.isAuthenticated">
                            <span class="nav-link text-muted">{{ authStore.user?.name }}</span>
                            <button class="btn btn-outline-danger btn-sm px-3 rounded-pill" @click="handleLogout"
                                :disabled="isLoggingOut">
                                <span v-if="isLoggingOut" class="spinner-border spinner-border-sm me-2"></span>
                                {{ isLoggingOut ? "Đang xuất..." : "Đăng xuất" }}
                            </button>
                        </template>
                        <template v-else>
                            <router-link class="btn btn-outline-secondary px-4 rounded-pill" to="/login">Đăng
                                nhập</router-link>
                            <router-link class="btn btn-primary px-4 rounded-pill shadow-sm" to="/register">Đăng
                                ký</router-link>
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
const availableCourses = ref([]);
const isLoggingOut = ref(false);
const isLoadingData = ref(false);

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

const loadData = async () => {
    isLoadingData.value = true;
    try {
        const lessonRes = await getLessons();
        const lessons = lessonRes.data || [];
        availableCourses.value = lessons;

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
        console.error('Failed to load menu data', error);
    } finally {
        isLoadingData.value = false;
    }
};

onMounted(() => {
    loadData();
});

// Removed goToTestPage logic because links are now rendered directly via router-link
</script>

<style scoped>
.client-layout {
    background-color: #f8f9fa;
}

.navbar {
    padding: 1rem 0;
}

.dropdown-menu {
    animation: fadeIn 0.3s ease;
}

.dropdown-scrollable {
    max-height: 200px;
    overflow-y: auto;
}

.dropdown-scrollable::-webkit-scrollbar {
    width: 6px;
}

.dropdown-scrollable::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.dropdown-scrollable::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.dropdown-scrollable::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.dropdown-item {
    font-size: 0.95rem;
    transition: background-color 0.2s;
}

.dropdown-item:hover {
    background-color: rgba(252, 116, 221, 0.05);
    color: #fc74dd;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
