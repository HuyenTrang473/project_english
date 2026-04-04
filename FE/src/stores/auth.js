import { defineStore } from "pinia";
import { ref, computed } from "vue";
import http from "@/api/axiosClient";

function parseStoredUser() {
  try {
    const raw = localStorage.getItem("user");
    return raw ? JSON.parse(raw) : null;
  } catch {
    localStorage.removeItem("user");
    return null;
  }
}

function normalizeToken(rawToken) {
  if (!rawToken || typeof rawToken !== "string") {
    return null;
  }

  const trimmed = rawToken.trim();
  if (!trimmed) {
    return null;
  }

  return trimmed.startsWith("Bearer ") ? trimmed.slice(7).trim() : trimmed;
}

export const useAuthStore = defineStore("auth", () => {
  // --- State ---
  const user = ref(parseStoredUser());
  const token = ref(normalizeToken(localStorage.getItem("token")));
  const initialized = ref(false);

  // --- Getters ---
  const isAuthenticated = computed(() => !!token.value);
  const userRole = computed(() => user.value?.role || null);
  const isAdmin = computed(() => userRole.value === "admin");
  const isTeacher = computed(() => userRole.value === "giao_vien");
  const isStudent = computed(() => userRole.value === "hoc_sinh");

  // --- Actions ---
  function setAuth(userData, accessToken) {
    const cleanToken = normalizeToken(accessToken);
    user.value = userData;
    token.value = cleanToken;
    localStorage.setItem("user", JSON.stringify(userData));
    if (cleanToken) {
      localStorage.setItem("token", cleanToken);
    } else {
      localStorage.removeItem("token");
    }
  }

  function clearAuth() {
    user.value = null;
    token.value = null;
    localStorage.removeItem("user");
    localStorage.removeItem("token");
  }

  async function login(credentials) {
    const response = await http.post("/login", credentials);
    setAuth(response.user, response.access_token);
    return response;
  }

  async function loginWithGoogle(credential) {
    const response = await http.post("/login/google", { credential });
    setAuth(response.user, response.access_token);
    return response;
  }

  async function register(payload) {
    const response = await http.post("/register", payload);
    setAuth(response.user, response.access_token);
    return response;
  }

  async function logout() {
    try {
      const response = await http.post("/logout");
      clearAuth();
      return {
        success: true,
        message: response.message || "Đăng xuất thành công",
      };
    } catch (error) {
      const message = error?.response?.data?.message || "Đăng xuất thất bại";
      clearAuth();
      return { success: false, message };
    }
  }

  async function fetchUser() {
    try {
      const response = await http.get("/auth/me");
      user.value = response.user;
      localStorage.setItem("user", JSON.stringify(response.user));
      return response.user;
    } catch (error) {
      const status = error?.response?.status;
      if (status === 401 || status === 403) {
        clearAuth();
      }
      return null;
    } finally {
      initialized.value = true;
    }
  }

  async function initializeAuth() {
    if (initialized.value) {
      return;
    }

    if (token.value) {
      await fetchUser(); // luôn verify token với server
      return;
    }

    initialized.value = true;
  }

  const defaultRouteByRole = computed(() => {
    if (userRole.value === "admin") return "/admin";
    return "/";
  });

  return {
    user,
    token,
    initialized,
    isAuthenticated,
    userRole,
    isAdmin,
    isTeacher,
    isStudent,
    defaultRouteByRole,
    setAuth,
    clearAuth,
    login,
    loginWithGoogle,
    register,
    logout,
    fetchUser,
    initializeAuth,
  };
});
