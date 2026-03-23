<template>
	<div class="nav-container primary-menu">
		<div class="mobile-topbar-header">
			<div>
				<img src="../../../assets/images/logo.png" class="logo-icon" alt="logo icon">
			</div>
			<div>
				<h4 class="logo-text">Admin</h4>
			</div>
			<div class="toggle-icon ms-auto"><i class='bx bx-arrow-to-left'></i>
			</div>
		</div>
		<nav class="navbar navbar-expand-xl w-100">
			<ul class="navbar-nav justify-content-start flex-grow-1 gap-1">
				<li class="nav-item dropdown">
					<a href="javascript:;" class="nav-link dropdown-toggle dropdown-toggle-nocaret"
						data-bs-toggle="dropdown">
						<div class="parent-icon"><i class="bx bx-home-circle"></i>
						</div>
						<div class="menu-title">Dashboard</div>
					</a>
					<ul class="dropdown-menu">
						<li>
							<a class="dropdown-item" href="index.html"><i class="bx bx-right-arrow-alt"></i>Default</a>
						</li>
					</ul>
				</li>
				<li class="nav-item">
					<router-link to="/admin/danh-muc">
						<a class="nav-link" href="/admin/danh-muc">
							<div class="parent-icon"><i class="fa-solid fa-bars"></i>
							</div>
							<div class="menu-title">Danh Mục</div>
						</a>
					</router-link>
				</li>
				<li class="nav-item">
					<router-link to="/lessons">
						<a class="nav-link" href="/lessons">
							<div class="parent-icon"><i class="fa-solid fa-book"></i>
							</div>
							<div class="menu-title">📚 Quản Lý Bài Học</div>
						</a>
					</router-link>
				</li>
			</ul>
		</nav>
	</div>
</template>
<script>
export default {

}
</script>
<style></style>