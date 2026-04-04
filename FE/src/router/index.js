import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const routes = [
  {
    path: "/",
    name: "Home",
    component: () => import("@/components/TrangChu/index.vue"),
    meta: { layout: "client" },
  },
  {
    path: "/login",
    name: "Login",
    component: () => import("@/views/auth/Login.vue"),
    meta: { layout: "client", guest: true },
  },
  {
    path: "/register",
    name: "Register",
    component: () => import("@/views/auth/Register.vue"),
    meta: { layout: "client", guest: true },
  },
  {
    path: "/auth/google/callback",
    name: "GoogleCallback",
    component: () => import("@/views/auth/GoogleCallback.vue"),
    meta: { layout: "client", guest: true },
  },
  // Admin Routes
  {
    path: "/admin",
    alias: "/home",
    component: () => import("@/components/Admin/index.vue"),
    meta: {
      layout: "admin",
      requiresAuth: true,
      roles: ["admin", "giao_vien"],
    },
    children: [],
  },
  // Teacher Routes
  {
    path: "/teacher/dashboard",
    name: "TeacherDashboard",
    component: () => import("@/components/TeacherDashboard.vue"),
    meta: { layout: "admin", requiresAuth: true, roles: ["giao_vien"] },
  },
  // Test Management Routes
  {
    path: "/tests",
    name: "TestList",
    component: () => import("@/views/TestList.vue"),
    meta: { layout: "admin", requiresAuth: true },
  },
  {
    path: "/tests/create-template",
    name: "CreateTestFromTemplate",
    component: () => import("@/views/QuizTemplateSelector.vue"),
    meta: {
      layout: "admin",
      requiresAuth: true,
      roles: ["giao_vien", "admin"],
    },
  },
  {
    path: "/tests/create",
    name: "CreateTest",
    component: () => import("@/views/TestBuilder.vue"),
    meta: {
      layout: "admin",
      requiresAuth: true,
      roles: ["giao_vien", "admin"],
    },
  },
  {
    path: "/tests/:id/edit",
    name: "EditTest",
    component: () => import("@/views/TestBuilder.vue"),
    meta: {
      layout: "admin",
      requiresAuth: true,
      roles: ["giao_vien", "admin"],
    },
  },
  {
    path: "/tests/:id/view",
    name: "ViewTest",
    component: () => import("@/views/TestBuilder.vue"),
    meta: {
      layout: "admin",
      requiresAuth: true,
      roles: ["giao_vien", "admin"],
    },
  },
  {
    path: "/tests/:id/take",
    name: "TakeTest",
    component: () => import("@/views/TestTaker.vue"),
    meta: { layout: "client", requiresAuth: true },
  },
  {
    path: "/tests/:id/result",
    name: "TestResult",
    component: () => import("@/views/ResultPage.vue"),
    meta: { layout: "client", requiresAuth: true },
  },
  {
    path: "/tests/:id/result/:attemptId",
    name: "StudentAttemptResult",
    component: () => import("@/views/ResultPage.vue"),
    meta: { layout: "client", requiresAuth: true },
  },
  {
    path: "/tests/:id/analytics",
    name: "TestAnalytics",
    component: () => import("@/views/AnalyticsDashboard.vue"),
    meta: {
      layout: "admin",
      requiresAuth: true,
      roles: ["giao_vien", "admin"],
    },
  },
  // Legacy Test Route
  {
    path: "/test/:testId",
    name: "DoTest",
    component: () => import("@/components/DoTest.vue"),
    meta: { layout: "client", requiresAuth: true },
  },
  // Lesson Routes
  {
    path: "/lessons",
    name: "LessonList",
    component: () => import("@/views/LessonList.vue"),
    meta: { layout: "client", requiresAuth: false },
  },
  // Specific routes BEFORE generic :id route
  {
    path: "/lessons/create",
    name: "CreateLesson",
    component: () => import("@/views/LessonEditor.vue"),
    meta: {
      layout: "admin",
      requiresAuth: true,
      roles: ["giao_vien", "admin"],
    },
  },
  {
    path: "/lessons/:id/edit",
    name: "EditLesson",
    component: () => import("@/views/LessonEditor.vue"),
    meta: {
      layout: "admin",
      requiresAuth: true,
      roles: ["giao_vien", "admin"],
    },
  },
  // Generic route LAST
  {
    path: "/lessons/:id",
    name: "LessonDetail",
    component: () => import("@/views/LessonDetail.vue"),
    meta: { layout: "client", requiresAuth: false },
  },
  // 404
  {
    path: "/:pathMatch(.*)*",
    name: "NotFound",
    component: () => import("@/views/NotFound.vue"),
    meta: { layout: "empty" },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes: routes,
});

// Navigation Guard — sử dụng Pinia auth store
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  await authStore.initializeAuth();

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: "Login", query: { redirect: to.fullPath } });
  } else if (to.meta.guest && authStore.isAuthenticated) {
    next({ name: "Home" });
  } else if (to.meta.roles && !to.meta.roles.includes(authStore.userRole)) {
    next({ name: "Home" });
  } else {
    next();
  }
});

export default router;
