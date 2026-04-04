<template>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4 fw-bold text-primary">Đăng nhập</h2>
                        <form @submit.prevent="handleLogin">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" v-model="form.email" required
                                    placeholder="name@example.com">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" v-model="form.password"
                                    required placeholder="Enter your password">
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" :disabled="loading">
                                    <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                                    Đăng nhập
                                </button>
                            </div>

                            <div class="text-center my-3 text-muted small">hoặc</div>
                            <div class="d-grid">
                                <button type="button" class="btn btn-outline-dark" :disabled="loading"
                                    @click="handleGoogleLoginRedirect">
                                    Tiếp tục với Google
                                </button>
                            </div>

                            <!-- Display Errors Dynamically -->
                            <div v-if="apiError"
                                class="alert alert-danger mt-3 mb-0 small rounded-3 border-0 shadow-sm py-2">
                                {{ apiError }}
                            </div>

                        </form>
                        <hr class="my-4">
                        <div class="text-center">
                            <p class="mb-0">Chưa có tài khoản? <router-link to="/register"
                                    class="text-primary text-decoration-none">Đăng ký ngay</router-link></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const loading = ref(false);
const apiError = ref('');
const form = ref({
    email: '',
    password: ''
});

const extractErrorMessage = (error) => {
    const data = error?.response?.data;
    if (data?.errors) {
        const firstKey = Object.keys(data.errors)[0];
        return data.errors[firstKey]?.[0] || 'Dữ liệu không hợp lệ.';
    }
    if (data?.message) {
        return data.message;
    }
    return 'Đăng nhập thất bại do kết nối máy chủ.';
};

const handleLogin = async () => {
    loading.value = true;
    apiError.value = '';
    try {
        await authStore.login(form.value);
        const redirectPath = router.currentRoute.value.query.redirect || authStore.defaultRouteByRole;
        router.push(redirectPath);
    } catch (error) {
        apiError.value = extractErrorMessage(error);
    } finally {
        loading.value = false;
    }
};

const handleGoogleLoginRedirect = () => {
    const base = (import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api').replace(/\/$/, '');
    window.location.href = `${base}/auth/google/redirect`;
};
</script>
