<template>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 text-center">
                        <h2 class="mb-3 fw-bold text-primary">Đang xử lý đăng nhập Google</h2>
                        <div v-if="loading" class="spinner-border text-primary" role="status"></div>
                        <div v-if="message"
                            class="alert alert-warning mt-3 mb-0 small rounded-3 border-0 shadow-sm py-2">
                            {{ message }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const loading = ref(true);
const message = ref('');

const decodeUser = (encodedUser) => {
    if (!encodedUser || typeof encodedUser !== 'string') {
        return null;
    }

    try {
        const normalized = encodedUser.replace(/-/g, '+').replace(/_/g, '/');
        const padded = normalized + '='.repeat((4 - (normalized.length % 4)) % 4);
        const json = atob(padded);
        return JSON.parse(json);
    } catch {
        return null;
    }
};

onMounted(async () => {
    const token = typeof route.query.token === 'string' ? route.query.token : '';
    const user = decodeUser(typeof route.query.user === 'string' ? route.query.user : '');
    const error = typeof route.query.error === 'string' ? route.query.error : '';

    if (error) {
        message.value = 'Đăng nhập Google thất bại. Vui lòng thử lại.';
        loading.value = false;
        setTimeout(() => router.replace('/login'), 1200);
        return;
    }

    if (!token) {
        message.value = 'Không nhận được token đăng nhập từ Google.';
        loading.value = false;
        setTimeout(() => router.replace('/login'), 1200);
        return;
    }

    authStore.setAuth(user, token);

    if (!user) {
        await authStore.fetchUser();
    }

    const redirectPath = typeof route.query.redirect === 'string'
        ? route.query.redirect
        : authStore.defaultRouteByRole;

    router.replace(redirectPath || '/');
});
</script>
