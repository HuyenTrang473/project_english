import http from "./axiosClient";

/**
 * API cho module Bài Test — mapping đúng với BE route /api/bai-tests
 */

// ===== Public =====

/** Lấy danh sách bài test theo lesson (public) với search, filter, pagination */
export function getListByLesson(lessonId, params = {}) {
  return http.get(`/lessons/${lessonId}/bai-tests`, { params });
}

/** Lấy danh sách tất cả bài test (authenticated users) */
export function getAllTests(params = {}) {
  return http.get(`/bai-tests/all`, { params });
}

// ===== Student (auth:sanctum, role:hoc_sinh) =====

/** Lấy chi tiết bài test (kèm câu hỏi + đáp án) */
export function getDetail(id) {
  return http.get(`/bai-tests/${id}`);
}

/** Lấy chi tiết bài test cho giáo viên (edit mode - kèm đáp án đúng) */
export function getDetailForTeacher(id) {
  return http.get(`/bai-tests/${id}/edit`);
}

/** Bắt đầu làm bài test */
export function startTest(testId) {
  return http.post(`/bai-tests/${testId}/start`);
}

/** Nộp bài test */
export function submitTest(testId, answers) {
  return http.post(`/bai-tests/${testId}/submit`, { answers });
}

/** Xem kết quả */
export function getResult(testId) {
  return http.get(`/bai-tests/${testId}/result`);
}

// ===== Teacher (auth:sanctum, role:giao_vien) - Test Management =====

/** Tạo bài test mới */
export function createTest(data) {
  return http.post("/bai-tests", data);
}

/** Cập nhật bài test */
export function updateTest(id, data) {
  // FormData doesn't work with PUT in PHP/Laravel, use POST with _method override
  if (data instanceof FormData) {
    data.append('_method', 'PUT');
    return http.post(`/bai-tests/${id}`, data);
  }
  return http.put(`/bai-tests/${id}`, data);
}

/** Xoá bài test */
export function deleteTest(id) {
  return http.delete(`/bai-tests/${id}`);
}

/** Danh sách bài test của giáo viên */
export function getMyTests() {
  return http.get("/teacher/bai-tests");
}

// ===== Teacher (auth:sanctum, role:giao_vien) - Question Management =====

/** Lấy danh sách câu hỏi theo bài test */
export function getQuestionsByTest(testId) {
  return http.get(`/bai-tests/${testId}/cau-hois`);
}

/** Tạo câu hỏi mới */
export function createQuestion(testId, data) {
  return http.post(`/bai-tests/${testId}/cau-hois`, data);
}

/** Cập nhật câu hỏi */
export function updateQuestion(testId, questionId, data) {
  return http.put(`/bai-tests/${testId}/cau-hois/${questionId}`, data);
}

/** Xóa câu hỏi */
export function deleteQuestion(testId, questionId) {
  return http.delete(`/bai-tests/${testId}/cau-hois/${questionId}`);
}

// ===== Teacher (auth:sanctum, role:giao_vien) - Answer Management =====

/** Tạo đáp án mới */
export function createAnswer(testId, questionId, data) {
  return http.post(`/bai-tests/${testId}/cau-hois/${questionId}/dap-ans`, data);
}

/** Cập nhật đáp án */
export function updateAnswer(testId, questionId, answerId, data) {
  return http.put(
    `/bai-tests/${testId}/cau-hois/${questionId}/dap-ans/${answerId}`,
    data,
  );
}

/** Xóa đáp án */
export function deleteAnswer(testId, questionId, answerId) {
  return http.delete(
    `/bai-tests/${testId}/cau-hois/${questionId}/dap-ans/${answerId}`,
  );
}

// ===== Teacher (auth:sanctum, role:giao_vien) - My Tests (with pagination & filter) =====

/** Danh sách bài test của giáo viên (with pagination & filter) */
export function getMyTestsPaginated(params = {}) {
  return http.get("/teacher/bai-tests", { params });
}

// ===== Teacher (auth:sanctum, role:giao_vien) - Analytics =====

/** Lấy thống kê & analytics của bài test */
export function getTestAnalytics(testId) {
  return http.get(`/bai-tests/${testId}/analytics`);
}
