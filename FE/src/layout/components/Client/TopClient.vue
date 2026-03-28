<template>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top py-2">
        <div class="container-fluid">

            <!-- LOGO -->
            <router-link to="/" class="navbar-brand d-flex align-items-center gap-2" aria-label="Trang chủ ENG LEARN">
                <img src="../../../assets/images/logo.png" alt="ENG LEARN - English Learning Platform" width="75"
                    height="75" loading="lazy" />
                <span class="fw-bold fs-4 text-primary">ENG LEARN</span>
            </router-link>

            <!-- TOGGLE -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- MENU -->
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-3">

                    <!-- HOME -->
                    <li class="nav-item">
                        <router-link to="/" class="nav-link d-flex align-items-center gap-2">
                            <i class="fa-solid fa-house fs-5 text-primary"></i>
                            <span class="fw-semibold">Trang chủ</span>
                        </router-link>
                    </li>

                    <!-- LESSONS -->
                    <li class="nav-item">
                        <router-link to="/lessons" class="nav-link d-flex align-items-center gap-2">
                            <i class="fa-solid fa-book fs-5 text-info"></i>
                            <span class="fw-semibold">Bài Học</span>
                        </router-link>
                    </li>

                    <!-- DROPDOWN TEST -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="testDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-pen-to-square fs-5 text-danger"></i>
                            <span class="fw-semibold">Kiểm tra trình độ</span>
                        </a>
                        <ul class="dropdown-menu shadow-sm border-0 p-2" style="min-width: 280px;">
                            <template v-if="Object.keys(groupedTests).length > 0">
                                <template v-for="(group, key, index) in groupedTests" :key="key">
                                    <li v-if="index > 0"><hr class="dropdown-divider"></li>
                                    <li><h6 class="dropdown-header fw-bold text-primary px-3 fs-6">{{ group.label }}</h6></li>
                                    <li v-for="test in group.tests" :key="test.testId">
                                        <router-link class="dropdown-item rounded py-2 px-3 fw-medium" :to="`/test/${test.testId}`">
                                            {{ test.testName }}
                                        </router-link>
                                    </li>
                                </template>
                            </template>
                            <li v-else>
                                <span class="dropdown-item text-muted px-3 py-2">Chưa có bài test</span>
                            </li>
                        </ul>
                    </li>

                    <!-- CONTACT -->
                    <li class="nav-item">
                        <router-link to="/" class="nav-link d-flex align-items-center gap-2">
                            <i class="fa-solid fa-envelope fs-5 text-success"></i>
                            <span class="fw-semibold">Liên hệ</span>
                        </router-link>
                    </li>

                    <!-- FAQ -->
                    <li class="nav-item">
                        <router-link to="/" class="nav-link d-flex align-items-center gap-2">
                            <i class="fa-solid fa-circle-question fs-5 text-warning"></i>
                            <span class="fw-semibold">Câu hỏi thường gặp</span>
                        </router-link>
                    </li>
                </ul>

                <!-- ACTION BUTTON -->
                <div class="d-flex gap-2">
                    <template v-if="authStore.isAuthenticated">
                        <span class="nav-link text-muted d-flex align-items-center">{{ authStore.user?.name }}</span>
                        <button class="btn btn-outline-danger btn-sm rounded-pill px-4" @click="handleLogout" :disabled="isLoggingOut">
                          <span v-if="isLoggingOut" class="spinner-border spinner-border-sm me-2"></span>
                          {{ isLoggingOut ? 'Đang xuất...' : 'Đăng xuất' }}
                        </button>
                    </template>
                    <template v-else>
                        <router-link to="/register" class="btn btn-outline-primary rounded-pill px-4">
                            Đăng ký
                        </router-link>
                        <router-link to="/login" class="btn btn-primary rounded-pill px-4">
                            Đăng nhập
                        </router-link>
                    </template>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { getLessons } from '@/api/lessonApi';
import { getListByLesson } from '@/api/testApi';
import { useAuthStore } from '@/stores/auth';

export default {
    name: 'ClientNavbar',
    setup() {
        const router = useRouter();
        const authStore = useAuthStore();
        const availableTests = ref([]);
        const isLoggingOut = ref(false);

        const groupedTests = computed(() => {
            const groups = {
                listening: { label: '🎧 Nghe (Listening)', tests: [] },
                reading: { label: '📖 Đọc (Reading)', tests: [] },
                writing: { label: '✍️ Viết (Writing)', tests: [] },
                mixed: { label: '🎯 Hỗn Hợp (Mixed)', tests: [] },
                other: { label: '📋 Khác', tests: [] }
            };

            availableTests.value.forEach(test => {
                const type = test.loai_quiz || 'other';
                if (groups[type]) {
                    groups[type].tests.push(test);
                } else {
                    groups.other.tests.push(test);
                }
            });

            // Filter out empty groups
            const result = {};
            for (const [key, group] of Object.entries(groups)) {
                if (group.tests.length > 0) {
                    result[key] = group;
                }
            }
            return result;
        });

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
                                testName: testName,
                                loai_quiz: test.loai_quiz || 'mixed'
                            });
                        }
                    }
                }
            } catch (error) {
                console.error('Failed to load available tests', error);
            }
        };

        const handleLogout = async () => {
            isLoggingOut.value = true;
            try {
                const result = await authStore.logout();
                if (result.success) {
                    await router.push({ name: 'Login' });
                } else {
                    alert(result.message || 'Đăng xuất thất bại');
                }
            } catch (error) {
                console.error('Logout error:', error);
                alert('Đăng xuất bị lỗi');
            } finally {
                isLoggingOut.value = false;
            }
        };

        onMounted(() => {
            loadAvailableTests();
        });

        return {
            authStore,
            availableTests,
            groupedTests,
            isLoggingOut,
            handleLogout,
        };
    },
};
</script>

<style scoped>
.dropdown-menu {
    min-width: 220px;
}

.nav-link {
    transition: color 0.2s ease;
}

.nav-link:hover {
    color: #0d6efd;
}
</style>
