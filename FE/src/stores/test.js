import { defineStore } from "pinia";
import { ref, computed } from "vue";
import * as testApi from "@/api/testApi";

export const useTestStore = defineStore("test", () => {
  // State
  const tests = ref([]);
  const currentTest = ref(null);
  const currentAttempt = ref(null);
  const studentAnswers = ref([]);
  const testResult = ref(null);
  const analytics = ref(null);
  const loading = ref(false);
  const error = ref(null);
  const pagination = ref({
    total: 0,
    perPage: 15,
    currentPage: 1,
    lastPage: 1,
  });

  // Filters & Search
  const filters = ref({
    search: "",
    status: null,
    sortBy: "created_at",
    sortOrder: "desc",
    perPage: 15,
  });

  // Computed
  const filteredTests = computed(() => tests.value);
  const totalTime = computed(() => {
    if (!currentTest.value) return 0;
    return currentTest.value.thoi_gian_toi_da || 0;
  });
  const hasMoreAttempts = computed(() => {
    if (!currentTest.value) return false;
    if (currentTest.value.so_lan_lam_toi_da === 0) return true;
    // Logic for checking remaining attempts
    return true;
  });

  // Actions - List Tests
  const fetchTestsByLesson = async (lessonId, params = {}) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await testApi.getListByLesson(lessonId, params);
      tests.value = response.data || [];
      if (response.pagination) {
        pagination.value = response.pagination;
      }
    } catch (err) {
      error.value = err.response?.data?.message || "Failed to fetch tests";
    } finally {
      loading.value = false;
    }
  };

  const fetchMyTests = async (params = {}) => {
    loading.value = true;
    error.value = null;
    try {
      const finalParams = { ...filters.value, ...params };
      const response = await testApi.getMyTestsPaginated(finalParams);
      tests.value = response.data || [];
      if (response.pagination) {
        pagination.value = response.pagination;
      }
    } catch (err) {
      error.value = err.response?.data?.message || "Failed to fetch tests";
    } finally {
      loading.value = false;
    }
  };

  const fetchAllTests = async (params = {}) => {
    loading.value = true;
    error.value = null;
    try {
      const finalParams = { ...filters.value, ...params };
      const response = await testApi.getAllTests(finalParams);
      tests.value = response.data || [];
      if (response.pagination) {
        pagination.value = response.pagination;
      }
    } catch (err) {
      error.value = err.response?.data?.message || "Failed to fetch tests";
    } finally {
      loading.value = false;
    }
  };

  // Actions - Test Details
  const fetchTestDetail = async (testId) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await testApi.getDetail(testId);
      currentTest.value = response.data;
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || "Failed to fetch test";
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const fetchTestDetailForTeacher = async (testId) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await testApi.getDetailForTeacher(testId);
      currentTest.value = response.data;
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || "Failed to fetch test";
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Actions - Test Management (Teacher)
  const createTest = async (data) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await testApi.createTest(data);
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || "Failed to create test";
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const updateTest = async (testId, data) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await testApi.updateTest(testId, data);
      currentTest.value = response.data;
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || "Failed to update test";
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const deleteTest = async (testId) => {
    loading.value = true;
    error.value = null;
    try {
      await testApi.deleteTest(testId);
      tests.value = tests.value.filter((t) => t.id !== testId);
    } catch (err) {
      error.value = err.response?.data?.message || "Failed to delete test";
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Actions - Taking Test (Student)
  const startTest = async (testId) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await testApi.startTest(testId);
      currentAttempt.value = response.data;
      studentAnswers.value = [];
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || "Failed to start test";
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const submitTest = async (testId, answers) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await testApi.submitTest(testId, answers);
      testResult.value = response.data;
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || "Failed to submit test";
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const fetchResult = async (testId) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await testApi.getResult(testId);
      testResult.value = response.data;
    } catch (err) {
      error.value = err.response?.data?.message || "Failed to fetch result";
    } finally {
      loading.value = false;
    }
  };

  // Actions - Analytics
  const fetchAnalytics = async (testId) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await testApi.getTestAnalytics(testId);
      analytics.value = response.data;
    } catch (err) {
      error.value = err.response?.data?.message || "Failed to fetch analytics";
    } finally {
      loading.value = false;
    }
  };

  // Actions - Question Management
  const fetchQuestions = async (testId) => {
    try {
      const response = await testApi.getQuestionsByTest(testId);
      return response;
    } catch (err) {
      error.value = err.response?.data?.message || "Failed to fetch questions";
      throw err;
    }
  };

  const createQuestion = async (testId, data) => {
    try {
      const response = await testApi.createQuestion(testId, data);
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || "Failed to create question";
      throw err;
    }
  };

  const updateQuestion = async (testId, questionId, data) => {
    try {
      const response = await testApi.updateQuestion(testId, questionId, data);
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || "Failed to update question";
      throw err;
    }
  };

  const deleteQuestion = async (testId, questionId) => {
    try {
      await testApi.deleteQuestion(testId, questionId);
    } catch (err) {
      error.value = err.response?.data?.message || "Failed to delete question";
      throw err;
    }
  };

  // Actions - Answer Management
  const createAnswer = async (testId, questionId, data) => {
    try {
      const response = await testApi.createAnswer(testId, questionId, data);
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || "Failed to create answer";
      throw err;
    }
  };

  const updateAnswer = async (testId, questionId, answerId, data) => {
    try {
      const response = await testApi.updateAnswer(
        testId,
        questionId,
        answerId,
        data,
      );
      return response.data;
    } catch (err) {
      error.value = err.response?.data?.message || "Failed to update answer";
      throw err;
    }
  };

  const deleteAnswer = async (testId, questionId, answerId) => {
    try {
      await testApi.deleteAnswer(testId, questionId, answerId);
    } catch (err) {
      error.value = err.response?.data?.message || "Failed to delete answer";
      throw err;
    }
  };

  // Actions - Student Answers Tracking
  const recordAnswer = (questionId, answerId, answerText = null) => {
    const existingIndex = studentAnswers.value.findIndex(
      (a) => a.id_cau_hoi === questionId,
    );
    const answer = {
      id_cau_hoi: questionId,
      id_dap_an: answerId || null,
      cau_tra_loi_tu_do: answerText || null,
    };

    if (existingIndex >= 0) {
      studentAnswers.value[existingIndex] = answer;
    } else {
      studentAnswers.value.push(answer);
    }
  };

  // Actions - Filters
  const updateFilters = (newFilters) => {
    filters.value = { ...filters.value, ...newFilters };
  };

  const resetFilters = () => {
    filters.value = {
      search: "",
      status: null,
      sortBy: "created_at",
      sortOrder: "desc",
      perPage: 15,
    };
  };

  // Actions - Reset State
  const resetCurrentTest = () => {
    currentTest.value = null;
    studentAnswers.value = [];
  };

  const resetResult = () => {
    testResult.value = null;
  };

  const clearError = () => {
    error.value = null;
  };

  return {
    // State
    tests,
    currentTest,
    currentAttempt,
    studentAnswers,
    testResult,
    analytics,
    loading,
    error,
    pagination,
    filters,

    // Computed
    filteredTests,
    totalTime,
    hasMoreAttempts,

    // Actions - List
    fetchTestsByLesson,
    fetchMyTests,
    fetchAllTests,

    // Actions - Details
    fetchTestDetail,
    fetchTestDetailForTeacher,

    // Actions - Management
    createTest,
    updateTest,
    deleteTest,

    // Actions - Taking Test
    startTest,
    submitTest,
    fetchResult,

    // Actions - Analytics
    fetchAnalytics,

    // Actions - Questions
    fetchQuestions,
    createQuestion,
    updateQuestion,
    deleteQuestion,

    // Actions - Answers
    createAnswer,
    updateAnswer,
    deleteAnswer,

    // Actions - Tracking
    recordAnswer,

    // Actions - Filters
    updateFilters,
    resetFilters,

    // Actions - Reset
    resetCurrentTest,
    resetResult,
    clearError,
  };
});
