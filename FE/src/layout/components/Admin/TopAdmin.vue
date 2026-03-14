<template>
	<div class="topbar d-flex align-items-center">
		<nav class="navbar navbar-expand">
			<div class="topbar-logo-header">
				<div class="">
					<img src="../../../assets/images/logo.png" class="logo-icon mb-1" alt="logo icon">
				</div>
				<div class="">
					<h4 class="logo-text" style="color: red;">ADMIN</h4>
				</div>
			</div>
			<div class="mobile-toggle-menu"><i class='bx bx-menu'></i></div>
			<div class="search-bar flex-grow-1">
				<div class="position-relative search-bar-box">
					<input type="text" class="form-control search-control" placeholder="Tìm Kiếm?">
					<span class="position-absolute top-50 search-show translate-middle-y"><i
							class='bx bx-search'></i></span>
					<span class="position-absolute top-50 search-close translate-middle-y"><i
							class='bx bx-x'></i></span>
				</div>
			</div>
			<div class="user-box dropdown">
				<a class="d-flex align-items-center nav-link dropdown-toggle dropdown-toggle-nocaret" href="#"
					role="button" data-bs-toggle="dropdown" aria-expanded="false">
					<img src="../../../assets/images/logo.png" class="user-img" alt="user avatar">
					<div class="user-info ps-3">
						<p class="user-name mb-0">{{ authStore.user?.name }}</p>
						<p class="designattion mb-0">{{ authStore.userRole }}</p>
					</div>
				</a>
				<ul class="dropdown-menu dropdown-menu-end">
					<li v-if="authStore.userRole === 'giao_vien'">
						<router-link to="/teacher/dashboard">
							<a class="dropdown-item"><i class='bx bx-file'></i><span>Quản Lý Bài Thi</span></a>
						</router-link>
					</li>
					<li>
						<router-link to="/admin/profile">
							<a class="dropdown-item" href="/admin/profile"><i
									class="bx bx-user"></i><span>Hồ sơ</span></a>
						</router-link>
					</li>
					<li><a class="dropdown-item" href="#" @click.prevent="handleLogout" :disabled="isLoggingOut"><i
								class='bx bx-log-out-circle'></i><span>{{ isLoggingOut ? 'Đang xuất...' : 'Đăng xuất' }}</span></a>
					</li>
				</ul>
			</div>
		</nav>
	</div>
</template>
<script>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

export default {
	setup() {
		const router = useRouter();
		const authStore = useAuthStore();
		const isLoggingOut = ref(false);

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

		return {
			authStore,
			isLoggingOut,
			handleLogout
		};
	}
}
</script>
<style></style>