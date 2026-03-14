import axios from "axios";

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

const http = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || "http://localhost:8000/api",
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

// Request interceptor — gắn Bearer token vào mọi request
http.interceptors.request.use(
  function (config) {
    const token = normalizeToken(localStorage.getItem("token"));
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    } else if (config.headers && config.headers.Authorization) {
      delete config.headers.Authorization;
    }
    return config;
  },
  function (error) {
    return Promise.reject(error);
  },
);

// Response interceptor — trả về response.data, xử lý 401
http.interceptors.response.use(
  function (response) {
    return response.data;
  },
  function (error) {
    const status = error.response?.status;
    const url = error.config?.url || "";
    const isAuthAction = /\/login$|\/register$/.test(url);
    const hasToken = !!normalizeToken(localStorage.getItem("token"));

    if (status === 401 && hasToken && !isAuthAction) {
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      import("@/router").then(({ default: router }) => {
        if (router.currentRoute.value.name !== "Login") {
          router.push({ name: "Login" });
        }
      });
    }
    return Promise.reject(error);
  },
);

export default http;
