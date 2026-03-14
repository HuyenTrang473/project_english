<template>
    <div class="admin-layout d-flex flex-column min-vh-100">
        <nav class="navbar navbar-dark bg-dark">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1">Admin Dashboard</span>
                <div class="d-flex gap-2">
                    <router-link class="btn btn-sm btn-outline-light" to="/home">Dashboard</router-link>
                    <button class="btn btn-sm btn-outline-danger" @click="handleLogout" :disabled="isLoggingOut">
                      <span v-if="isLoggingOut" class="spinner-border spinner-border-sm me-2"></span>
                      {{ isLoggingOut ? "Đang xuất..." : "Logout" }}
                    </button>
                </div>
            </div>
        </nav>
        <div class="container-fluid flex-grow-1 d-flex p-0">
            <div class="bg-light border-end p-3" style="width: 250px; min-height: 100%;">
                <div class="list-group list-group-flush">
                    <router-link to="/home" class="list-group-item list-group-item-action bg-transparent">
                        📊 Dashboard
                    </router-link>
                    <router-link to="/tests" class="list-group-item list-group-item-action bg-transparent">
                        📝 Quản lý Đề thi
                    </router-link>
                    <!-- Add more admin links here -->
                </div>
            </div>
            <main class="p-4 flex-grow-1">
                <router-view></router-view>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const router = useRouter();
const authStore = useAuthStore();
const isLoggingOut = ref(false);

const handleLogout = async () => {
  isLoggingOut.value = true;
  try {
    const result = await authStore.logout();
    if (result.success) {
      await router.push({ name: "Login" });
    } else {
      alert(result.message || "Đăng xuất thất bại");
    }
  } catch (error) {
    console.error("Logout error:", error);
    alert("Đăng xuất bị lỗi");
  } finally {
    isLoggingOut.value = false;
  }
};
</script>

<style scoped>
.admin-layout {
    background-color: #fff;
}
</style>
