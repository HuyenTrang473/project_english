<template>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="mb-4 text-center">Đăng ký</h2>
                <div v-if="errors.api" class="alert alert-danger">{{ errors.api }}</div>
                <form @submit.prevent="handleSubmit" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Họ tên</label>
                        <input v-model="form.name" type="text" class="form-control"
                            :class="{ 'is-invalid': errors.name }" placeholder="Nguyễn Văn A" />
                        <div class="invalid-feedback">{{ errors.name }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input v-model="form.email" type="email" class="form-control"
                            :class="{ 'is-invalid': errors.email }" placeholder="you@example.com" />
                        <div class="invalid-feedback">{{ errors.email }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input v-model="form.password" type="password" class="form-control"
                            :class="{ 'is-invalid': errors.password }" />
                        <div class="invalid-feedback">{{ errors.password }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nhập lại mật khẩu</label>
                        <input v-model="form.password_confirmation" type="password" class="form-control"
                            :class="{ 'is-invalid': errors.password_confirmation }" />
                        <div class="invalid-feedback">{{ errors.password_confirmation }}</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Tạo tài khoản</button>
                </form>
                <p class="mt-3 text-center">
                    Đã có tài khoản?
                    <router-link to="/login">Đăng nhập</router-link>
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
const form = reactive({ name: '', email: '', password: '', password_confirmation: '' });
const errors = reactive({ name: '', email: '', password: '', password_confirmation: '', api: '' });

const validate = () => {
    errors.name = form.name ? '' : 'Họ tên không được để trống';
    errors.email = form.email ? (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email) ? '' : 'Email không hợp lệ') : 'Email không được để trống';
    errors.password = form.password ? '' : 'Mật khẩu không được để trống';
    errors.password_confirmation = form.password === form.password_confirmation ? '' : 'Mật khẩu không khớp';
    return !errors.name && !errors.email && !errors.password && !errors.password_confirmation;
};

const handleSubmit = async () => {
    if (!validate()) return;
    try {
        errors.api = '';
        await http.post('/auth/register', {
            name: form.name,
            email: form.email,
            password: form.password,
            password_confirmation: form.password_confirmation,
        });
        router.push('/login');
    } catch (e) {
        errors.api = 'Đăng ký thất bại, vui lòng thử lại.';
    }
};
</script>
