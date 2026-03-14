<template>
    <div class="d-flex flex-column align-items-center justify-content-center min-vh-100 bg-light">
        <h1 class="display-1 fw-bold text-primary">404</h1>
        <p class="lead mb-4">Oops! Trang bạn tìm kiếm không tồn tại.</p>
        <router-link to="/" class="btn btn-primary">
            <i class="fas fa-home me-2"></i>Về trang chủ
        </router-link>
    </div>
</template>

<script>
export default {
    name: "NotFound"
}
</script>
