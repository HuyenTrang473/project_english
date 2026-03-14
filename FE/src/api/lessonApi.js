import http from "./axiosClient";

/** Lấy danh sách bài học đã published (public) */
export function getLessons() {
  return http.get("/lessons");
}

/** Lấy chi tiết bài học (public) */
export function getLessonDetail(id) {
  return http.get(`/lessons/${id}`);
}

/** Tạo bài học (teacher) */
export function createLesson(data) {
  return http.post("/lessons", data);
}

/** Cập nhật bài học (teacher) */
export function updateLesson(id, data) {
  return http.put(`/lessons/${id}`, data);
}

/** Xoá bài học (teacher) */
export function deleteLesson(id) {
  return http.delete(`/lessons/${id}`);
}

/** Danh sách bài học của giáo viên */
export function getMyLessons() {
  return http.get("/teacher/lessons");
}
