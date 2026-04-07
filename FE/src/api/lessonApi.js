import http from "./axiosClient";

/** Lấy danh sách bài học đã published (public) */
export function getLessons() {
  return http.get("/lessons");
}

/** Lấy chi tiết bài học (public) */
export function getLessonDetail(id) {
  return http.get(`/lessons/${id}`);
}

/** Lọc bài học (public) */
export function filterLessons(filters) {
  return http.get("/lessons/filter", { params: filters });
}

/** Danh sách bài học theo giáo viên (public) */
export function getLessonsByTeacher(teacherId, filters = {}) {
  return http.get(`/lessons/teacher/${teacherId}`, { params: filters });
}

/** Tạo bài học (teacher) */
export function createLesson(data) {
  // If data is FormData (contains file), just pass it as-is
  // Axios will automatically set Content-Type: multipart/form-data with proper boundary
  return http.post("/lessons", data);
}

/** Cập nhật bài học (teacher) */
export function updateLesson(id, data) {
  // If data is FormData (contains file), send as POST with _method=PUT
  // Axios will automatically set Content-Type: multipart/form-data with proper boundary
  if (data instanceof FormData) {
    data.append("_method", "PUT");
    return http.post(`/lessons/${id}`, data);
  }
  return http.put(`/lessons/${id}`, data);
}

/** Xoá bài học (teacher) */
export function deleteLesson(id) {
  return http.delete(`/lessons/${id}`);
}

/** Danh sách bài học của giáo viên */
export function getMyLessons(filters = {}) {
  return http.get("/teacher/lessons", { params: filters });
}

/** Danh sách bài học toàn hệ thống (admin/teacher) */
export function getAdminLessons(filters = {}) {
  return http.get("/admin/lessons", { params: filters });
}

/** Lấy chi tiết bài học để edit (teacher) */
export function getLessonForEdit(id) {
  return http.get(`/lessons/${id}/edit`);
}

/** Toggle publish/draft status */
export function toggleLessonStatus(id) {
  return http.patch(`/lessons/${id}/toggle-status`);
}
