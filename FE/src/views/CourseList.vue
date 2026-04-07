<template>
    <section class="courses-page py-5">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4">
                <div class="mb-3 mb-md-0">
                    <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill mb-2 fw-semibold">Optimal
                        Path</span>
                    <h1 class="h2 fw-bold mb-2 text-dark">All Published Courses</h1>
                    <p class="text-muted mb-0">Danh sach tat ca bai hoc da publish, giao dien giong trang chu.</p>
                </div>
                <router-link to="/" class="btn btn-outline-primary rounded-pill px-4 fw-medium">
                    <i class="fa-solid fa-arrow-left me-2"></i> Back To Home
                </router-link>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3 p-md-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-7">
                            <input v-model.trim="search" type="text" class="form-control rounded-pill px-4"
                                placeholder="Tim kiem bai hoc..." @input="onSearchInput">
                        </div>
                        <div class="col-12 col-md-3">
                            <select v-model="sortBy" class="form-select rounded-pill px-3" @change="loadCourses(1)">
                                <option value="created_at">Moi nhat</option>
                                <option value="tieu_de">Ten A-Z</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <select v-model.number="perPage" class="form-select rounded-pill px-3"
                                @change="loadCourses(1)">
                                <option :value="6">6 / page</option>
                                <option :value="9">9 / page</option>
                                <option :value="12">12 / page</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="loading" class="text-center py-5 text-muted">
                Dang tai bai hoc...
            </div>

            <div v-else-if="courses.length === 0" class="text-center py-5">
                <div class="bg-light p-5 rounded-4 border border-dashed">
                    <p class="text-muted fs-5 mb-0">Hien tai chua co bai hoc duoc cong bo.</p>
                </div>
            </div>

            <div v-else class="row g-4">
                <div v-for="course in courses" :key="course.id" class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden course-card bg-white">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-center gap-2 mb-4">
                                <span
                                    class="badge bg-soft-info text-info rounded-pill px-3 py-2 fw-medium border border-info border-opacity-25">
                                    <i class="fas fa-book me-1"></i> Course
                                </span>
                            </div>

                            <h5 class="card-title fw-bold mb-3 text-dark">{{ course.tieu_de }}</h5>
                            <p class="card-text text-muted small mb-4 flex-grow-1 lh-lg">
                                {{ course.mo_ta || 'Improve your English skills with this comprehensive course.' }}
                            </p>

                            <div
                                class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top border-light">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 32px; height: 32px;">
                                        <i class="fas fa-user text-secondary small"></i>
                                    </div>
                                    <span class="text-dark fw-medium small">{{ course.giao_vien?.name || 'Teacher'
                                        }}</span>
                                </div>
                                <button class="btn btn-primary btn-sm rounded-pill px-4 py-2 fw-semibold shadow-sm"
                                    @click="goToCourse(course.id)">
                                    <i class="fas fa-arrow-right me-2"></i> View
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <nav v-if="pagination.last_page > 1" class="mt-5">
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                        <button class="page-link" :disabled="pagination.current_page === 1"
                            @click="loadCourses(pagination.current_page - 1)">
                            Previous
                        </button>
                    </li>

                    <li v-for="page in pageNumbers" :key="page" class="page-item"
                        :class="{ active: page === pagination.current_page }">
                        <button class="page-link" @click="loadCourses(page)">{{ page }}</button>
                    </li>

                    <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                        <button class="page-link" :disabled="pagination.current_page === pagination.last_page"
                            @click="loadCourses(pagination.current_page + 1)">
                            Next
                        </button>
                    </li>
                </ul>
            </nav>
        </div>
    </section>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import { filterLessons } from "@/api/lessonApi";

const router = useRouter();

const courses = ref([]);
const loading = ref(false);
const search = ref("");
const sortBy = ref("created_at");
const perPage = ref(12);
const searchTimeout = ref(null);

const pagination = ref({
    total: 0,
    per_page: 12,
    current_page: 1,
    last_page: 1,
});

const pageNumbers = computed(() => {
    const total = pagination.value.last_page || 1;
    const current = pagination.value.current_page || 1;
    const max = 5;

    let start = Math.max(1, current - Math.floor(max / 2));
    let end = Math.min(total, start + max - 1);

    if (end - start < max - 1) {
        start = Math.max(1, end - max + 1);
    }

    return Array.from({ length: end - start + 1 }, (_, i) => start + i);
});

const loadCourses = async (page = 1) => {
    loading.value = true;
    try {
        const res = await filterLessons({
            search: search.value || undefined,
            sort_by: sortBy.value,
            sort_order: "desc",
            page_size: perPage.value,
            page,
        });

        courses.value = res.data || [];
        pagination.value = res.pagination || {
            total: courses.value.length,
            per_page: perPage.value,
            current_page: page,
            last_page: 1,
        };
    } catch (error) {
        console.error("Failed to load courses", error);
        courses.value = [];
    } finally {
        loading.value = false;
    }
};

const onSearchInput = () => {
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value);
    }

    searchTimeout.value = setTimeout(() => {
        loadCourses(1);
    }, 400);
};

const goToCourse = (courseId) => {
    router.push(`/lessons/${courseId}`);
};

onMounted(() => {
    loadCourses(1);
});
</script>

<style scoped>
.courses-page {
    min-height: 70vh;
}

.course-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.course-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.75rem 1.75rem rgba(0, 0, 0, 0.08) !important;
}

.bg-soft-primary {
    background-color: rgba(13, 110, 253, 0.08) !important;
}

.bg-soft-info {
    background-color: rgba(13, 202, 240, 0.08) !important;
}

.border-dashed {
    border-style: dashed !important;
}
</style>
