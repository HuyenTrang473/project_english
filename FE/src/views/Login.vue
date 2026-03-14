<template>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="mb-4 text-center">Đăng nhập</h2>
                <div v-if="errors.api" class="alert alert-danger">{{ errors.api }}</div>
                <form @submit.prevent="handleSubmit" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input v-model="form.email" type="email" class="form-control"
                            :class="{ 'is-invalid': errors.email }" placeholder="you@example.com" />
                        <div class="invalid-feedback">{{ errors.email }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input v-model="form.password" type="password" class="form-control"
                            :class="{ 'is-invalid': errors.password }" placeholder="••••••" />
                        <div class="invalid-feedback">{{ errors.password }}</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
                </form>
                <p class="mt-3 text-center">
                    Chưa có tài khoản?
                    <router-link to="/register">Đăng ký</router-link>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive } from 'vue';
import { useRouter } from 'vue-router';
import http from '@/api/axiosClient';

const router = useRouter();
const form = reactive({ email: '', password: '' });
const errors = reactive({ email: '', password: '', api: '' });

const validate = () => {
    errors.email = form.email ? (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email) ? '' : 'Email không hợp lệ') : 'Email không được để trống';
    errors.password = form.password ? '' : 'Mật khẩu không được để trống';
    return !errors.email && !errors.password;
};

const handleSubmit = async () => {
    if (!validate()) return;
    try {
        errors.api = '';
        await http.post('/auth/login', { email: form.email, password: form.password });
        router.push('/');
    } catch (e) {
        errors.api = 'Đăng nhập thất bại, vui lòng kiểm tra thông tin.';
    }
};
</script>
