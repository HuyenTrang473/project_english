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

                    <!-- DROPDOWN TEST -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="testDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-pen-to-square fs-5 text-danger"></i>
                            <span class="fw-semibold">Kiểm tra trình độ</span>
                        </a>
                        <ul class="dropdown-menu shadow-sm border-0">
                            <li>
                                <router-link class="dropdown-item" :to="testRoute">
                                    📝 Test IELTS
                                </router-link>
                            </li>
                            <li v-if="false">
                                <router-link class="dropdown-item" to="/test/toeic">
                                    📄 Test TOEIC
                                </router-link>
                            </li>
                            <li v-if="false">
                                <router-link class="dropdown-item" to="/test/level">
                                    🎯 Test trình độ tổng quát
                                </router-link>
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
        const featuredTestId = ref(null);
        const isLoggingOut = ref(false);
        const testRoute = computed(() => (featuredTestId.value ? `/test/${featuredTestId.value}` : '/'));

        const loadFeaturedTestId = async () => {
            try {
                const lessonRes = await getLessons();
                const lessons = lessonRes.data || [];
                for (const lesson of lessons) {
                    const testRes = await getListByLesson(lesson.id);
                    const tests = testRes.data || [];
                    if (tests.length > 0) {
                        featuredTestId.value = tests[0].id;
                        return;
                    }
                }
            } catch (error) {
                console.error('Failed to resolve featured test route', error);
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
            loadFeaturedTestId();
        });

        return {
            authStore,
            testRoute,
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
