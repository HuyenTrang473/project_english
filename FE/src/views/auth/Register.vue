<template>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4 fw-bold text-primary">Đăng ký</h2>
                        <form @submit.prevent="handleRegister">
                            <div class="mb-3">
                                <label for="fullName" class="form-label">Họ và tên</label>
                                <input type="text" class="form-control" id="fullName" v-model="form.name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" v-model="form.email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" v-model="form.password"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirmPassword"
                                    v-model="confirmPassword" required>
                                <div v-if="passwordMismatch" class="text-danger small mt-1">Mật khẩu không khớp</div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" :disabled="loading || passwordMismatch">
                                    <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                                    Đăng ký
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
                            <p class="mb-0">Đã có tài khoản? <router-link to="/login"
                                    class="text-primary text-decoration-none">Đăng nhập</router-link></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const loading = ref(false);
const confirmPassword = ref('');
const apiError = ref('');
const form = ref({
    name: '',
    email: '',
    password: ''
});

const passwordMismatch = computed(() => {
    return form.value.password && confirmPassword.value && form.value.password !== confirmPassword.value;
});

const handleRegister = async () => {
    if (passwordMismatch.value) return;

    loading.value = true;
    apiError.value = '';
    try {
        await authStore.register({
            ...form.value,
            password_confirmation: confirmPassword.value,
            passwordConfirmation: confirmPassword.value
        });
        const redirectPath = router.currentRoute.value.query.redirect || authStore.defaultRouteByRole;
        router.push(redirectPath);
    } catch (error) {
        let errorMsg = 'Đăng ký thất bại do kết nối máy chủ.';
        if (error.response && error.response.data) {
            if (error.response.data.errors) {
                const firstKey = Object.keys(error.response.data.errors)[0];
                errorMsg = error.response.data.errors[firstKey][0];
            } else if (error.response.data.message) {
                errorMsg = error.response.data.message;
            }
        }
        apiError.value = errorMsg;
    } finally {
        loading.value = false;
    }
};
</script>
