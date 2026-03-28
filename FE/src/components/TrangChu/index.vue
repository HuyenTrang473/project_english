<script setup>
import { ref, onMounted } from 'vue';
import { getLessons } from '@/api/lessonApi';
import { getListByLesson, getAllTests } from '@/api/testApi';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const courses = ref([]);
const publishedTests = ref([]);
const loading = ref(true);
const featuredTestId = ref(null);

const resolveFeaturedTestId = async (lessons) => {
    if (!lessons || lessons.length === 0) {
        return;
    }
    for (const lesson of lessons) {
        try {
            const testRes = await getListByLesson(lesson.id);
            const tests = testRes.data || [];
            if (tests.length > 0) {
                featuredTestId.value = tests[0].id;
                return;
            }
        } catch (error) {
            console.error(`Failed to get tests for lesson ${lesson.id}:`, error);
        }
    }
};

const fetchCourses = async () => {
    loading.value = true;
    try {
        const res = await getLessons();
        courses.value = res.data || [];
        console.log('Courses loaded:', courses.value);
        await resolveFeaturedTestId(courses.value);
        console.log('Featured test ID:', featuredTestId.value);
        await loadPublishedTests();
    } catch (error) {
        console.error("Failed to load courses", error);
    } finally {
        loading.value = false;
    }
};

const loadPublishedTests = async () => {
    try {
        const res = await getAllTests({ status: 2, per_page: 6, sort_by: 'created_at', sort_order: 'desc' });
        publishedTests.value = res.data || [];
        console.log('Published tests loaded:', publishedTests.value);
    } catch (error) {
        console.error('Failed to load tests', error);
    }
};

const goToTest = (testId) => {
    if (!authStore.isAuthenticated) {
        router.push({ name: 'Login', query: { redirect: `/test/${testId}` } });
        return;
    }
    router.push(`/test/${testId}`);
};

const goToFreeTest = () => {
    if (!authStore.isAuthenticated) {
        router.push({ name: 'Login', query: { redirect: featuredTestId.value ? `/test/${featuredTestId.value}` : '/test' } });
        return;
    }

    if (featuredTestId.value) {
        router.push(`/test/${featuredTestId.value}`);
        return;
    }

    alert('Hiện chưa có đề thi đã publish để làm thử.');
};

const scrollToRegister = () => {
    const el = document.getElementById('register-section');
    if (el) el.scrollIntoView({ behavior: 'smooth' });
};

const navigateToRegister = () => {
    router.push('/register');
};

const handleImageError = (event) => {
    event.target.src = 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?w=500';
};

onMounted(() => {
    fetchCourses();
});
</script>

<template>
    <div class="home-page">

        <!-- Hero Section -->
        <section class="hero-section position-relative overflow-hidden py-5">
            <div class="container py-lg-5">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-5 mb-lg-0">
                        <div class="hero-content pe-lg-5">
                            <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill mb-3 fw-semibold">
                                <i class="fas fa-star me-2"></i>#1 English Learning Platform
                            </span>
                            <h1 class="hero-title display-3 fw-bold mb-4 text-dark lh-sm">
                                Master English with <span class="highlight text-gradient">Confidence</span>
                            </h1>
                            <p class="hero-subtitle lead text-secondary mb-5">
                                Join thousands of students achieving their goals with our premium IELTS and
                                communication courses.
                                Start your journey to fluency today.
                            </p>
                            <div class="hero-actions d-flex flex-wrap gap-3">
                                <button
                                    class="btn btn-primary btn-lg rounded-pill px-5 py-3 shadow-sm hover-lift fw-semibold"
                                    @click="scrollToRegister">
                                    Start Learning Now
                                </button>
                                <button
                                    class="btn btn-outline-dark btn-lg rounded-pill px-5 py-3 hover-lift fw-semibold"
                                    @click="goToFreeTest">
                                    Take Free Test
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 position-relative">
                        <div class="hero-image-wrapper ms-lg-auto" style="max-width: 550px;">
                            <div class="image-bg-shape position-absolute top-50 start-50 translate-middle w-100 h-100 rounded-circle bg-soft-primary opacity-50"
                                style="filter: blur(60px); z-index: -1;"></div>
                            <img src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f"
                                alt="Learning English"
                                class="img-fluid rounded-5 shadow-xxl position-relative z-1 hover-scale">

                            <!-- Floating Cards -->
                            <div class="floating-card c1 bg-white p-3 rounded-4 shadow-sm position-absolute top-0 start-0 mt-4 ms-n4 z-2 border border-light"
                                style="backdrop-filter: blur(10px); background: rgba(255,255,255,0.9);">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-square bg-primary text-white p-2 rounded-3">
                                        <i class="fas fa-graduation-cap fa-lg"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 small text-muted text-uppercase fw-semibold"
                                            style="letter-spacing: 0.5px;">Target</p>
                                        <p class="mb-0 fw-bold text-dark fs-5">IELTS 8.0+</p>
                                    </div>
                                </div>
                            </div>

                            <div class="floating-card c2 bg-white p-3 rounded-4 shadow-sm position-absolute bottom-0 end-0 mb-4 me-n4 z-2 border border-light"
                                style="backdrop-filter: blur(10px); background: rgba(255,255,255,0.9);">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-square bg-success text-white p-2 rounded-3">
                                        <i class="fas fa-fire fa-lg"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 small text-muted text-uppercase fw-semibold"
                                            style="letter-spacing: 0.5px;">Result</p>
                                        <p class="mb-0 fw-bold text-dark fs-5">Fast Track</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Row -->
                <div class="row mt-5 pt-5 border-top border-light opacity-75">
                    <div class="col-6 col-md-4">
                        <div class="d-flex align-items-center gap-3">
                            <h3 class="fw-bolder mb-0 display-6 text-primary">10k+</h3>
                            <p class="mb-0 text-muted small lh-sm fw-medium">Active<br>Students</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="d-flex align-items-center gap-3">
                            <h3 class="fw-bolder mb-0 display-6 text-primary">98%</h3>
                            <p class="mb-0 text-muted small lh-sm fw-medium">Success<br>Rate</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 mt-4 mt-md-0">
                        <div class="d-flex align-items-center gap-3">
                            <h3 class="fw-bolder mb-0 display-6 text-primary">50+</h3>
                            <p class="mb-0 text-muted small lh-sm fw-medium">Expert<br>Tutors</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section py-5 position-relative bg-light" style="z-index: 2;">
            <div class="container py-4">
                <div class="text-center mb-5">
                    <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill mb-2 fw-semibold">Outstanding
                        Features</span>
                    <h2 class="h2 fw-bold text-dark mb-0">Comprehensive Learning Ecosystem</h2>
                </div>
                <div class="row g-4 justify-content-center">
                    <div class="col-md-4">
                        <div class="feature-card bg-white p-4 rounded-4 shadow-sm h-100 transition-hover border-0">
                            <div class="icon-box i-blue mb-4 rounded-3 d-flex align-items-center justify-content-center"
                                style="width: 56px; height: 56px; background: #e3f2fd; color: #2196f3; font-size: 1.5rem;">
                                <i class="fa-solid fa-earth-americas"></i>
                            </div>
                            <h3 class="h5 fw-bold mb-3 text-dark">IELTS Prep</h3>
                            <p class="text-muted small mb-0 lh-lg">Comprehensive verified curriculum for reading,
                                writing, listening, and speaking.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card bg-white p-4 rounded-4 shadow-sm h-100 transition-hover border-0">
                            <div class="icon-box i-pink mb-4 rounded-3 d-flex align-items-center justify-content-center"
                                style="width: 56px; height: 56px; background: #fff0fa; color: #fc74dd; font-size: 1.5rem;">
                                <i class="fa-solid fa-comments"></i>
                            </div>
                            <h3 class="h5 fw-bold mb-3 text-dark">Communication</h3>
                            <p class="text-muted small mb-0 lh-lg">Practice real-world conversations with native
                                speakers and expert tutors.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card bg-white p-4 rounded-4 shadow-sm h-100 transition-hover border-0">
                            <div class="icon-box i-green mb-4 rounded-3 d-flex align-items-center justify-content-center"
                                style="width: 56px; height: 56px; background: #e8f5e9; color: #4caf50; font-size: 1.5rem;">
                                <i class="fa-solid fa-certificate"></i>
                            </div>
                            <h3 class="h5 fw-bold mb-3 text-dark">Certification</h3>
                            <p class="text-muted small mb-0 lh-lg">Get recognized certificates upon completion of each
                                level.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section class="about-section py-5 my-md-5">
            <div class="container">
                <div class="row align-items-center gx-lg-5">
                    <div class="col-lg-6 mb-5 mb-lg-0">
                        <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill mb-3 fw-semibold">About
                            Us</span>
                        <h2 class="display-6 fw-bold mb-4 lh-base text-dark">Why Choose Us?</h2>
                        <p class="lead text-muted mb-5">We combine technology with expert pedagogy to deliver the most
                            effective learning experience.</p>
                        <ul class="list-unstyled">
                            <li class="mb-4 d-flex align-items-center gap-3">
                                <div class="icon-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center rounded-circle"
                                    style="width: 40px; height: 40px;">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <span class="fw-medium text-dark">Personalized Learning Path</span>
                            </li>
                            <li class="mb-4 d-flex align-items-center gap-3">
                                <div class="icon-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center rounded-circle"
                                    style="width: 40px; height: 40px;">
                                    <i class="fa-solid fa-book-open"></i>
                                </div>
                                <span class="fw-medium text-dark">24/7 Access to Materials</span>
                            </li>
                            <li class="mb-0 d-flex align-items-center gap-3">
                                <div class="icon-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center rounded-circle"
                                    style="width: 40px; height: 40px;">
                                    <i class="fa-solid fa-video"></i>
                                </div>
                                <span class="fw-medium text-dark">Live Interactive Sessions</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-6 position-relative">
                        <div class="position-absolute w-100 h-100 bg-soft-primary rounded-4"
                            style="top: 20px; left: -20px; z-index: -1;"></div>
                        <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644" alt="Team"
                            class="img-fluid rounded-4 shadow-sm object-fit-cover" style="height: 500px; width: 100%;">
                    </div>
                </div>
            </div>
        </section>

        <!-- Courses Section -->
        <section class="courses-section py-5 bg-light my-5">
            <div class="container py-lg-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5">
                    <div class="mb-3 mb-md-0">
                        <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill mb-2 fw-semibold">Optimal
                            Path</span>
                        <h2 class="h2 fw-bold mb-0 text-dark">Our Popular Courses</h2>
                    </div>
                    <router-link to="/lessons" class="btn btn-outline-primary rounded-pill px-4 fw-medium">
                        View All Courses <i class="fa-solid fa-arrow-right list-inline-item ms-1"></i>
                    </router-link>
                </div>

                <div v-if="loading" class="text-center py-5 text-muted">Loading courses...</div>

                <div v-else class="row g-4">
                    <div v-for="(course, index) in courses" :key="index" class="col-md-6 col-lg-4">
                        <div
                            class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden course-card transition-hover">
                            <div class="position-relative">
                                <img :src="course.hinh_anh || 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?w=500'"
                                    class="card-img-top object-fit-cover" alt="Course Image" style="height: 220px;"
                                    @error="handleImageError">
                                <span
                                    class="position-absolute top-0 end-0 m-3 badge bg-primary bg-gradient rounded-pill px-3 py-2 shadow-sm fw-medium">Hot</span>
                            </div>
                            <div class="card-body p-4 d-flex flex-column">
                                <h3 class="card-title h5 fw-bold text-dark mb-3">{{ course.title }}</h3>
                                <p class="card-text text-muted small mb-4 flex-grow-1 lh-lg">{{ course.description }}
                                </p>
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-soft-primary px-2 py-1 rounded-2 me-2">
                                        <i class="fa-solid fa-user-tie text-primary small"></i>
                                    </div>
                                    <span class="text-dark fw-medium small">Teacher: {{ course.teacher?.name ||
                                        'Pending' }}</span>
                                </div>
                                <button class="btn btn-primary w-100 rounded-pill fw-semibold shadow-sm py-2"
                                    @click="navigateToRegister">Register Now</button>
                            </div>
                        </div>
                    </div>
                    <!-- Mock Data if empty -->
                    <div v-if="courses.length === 0" class="col-12 text-center text-muted py-5">
                        <p>No courses available right now. Check back later!</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tests Section -->
        <section class="tests-section py-5 mb-5">
            <div class="container">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5">
                    <div class="mb-3 mb-md-0">
                        <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill mb-2 fw-semibold">Skill
                            Assessment</span>
                        <h2 class="h2 fw-bold mb-2 text-dark">Take a Quick Test</h2>
                        <p class="text-muted mb-0 fs-6">Evaluate your skills instantly with our intelligent grading
                            system</p>
                    </div>
                    <router-link to="/tests" class="btn btn-outline-primary rounded-pill px-4 fw-medium">
                        All Tests <i class="fa-solid fa-arrow-right list-inline-item ms-1"></i>
                    </router-link>
                </div>

                <div v-if="publishedTests.length === 0" class="text-center py-5">
                    <div class="bg-light p-5 rounded-4 border border-dashed">
                        <p class="text-muted fs-5 mb-0">Currently no tests are published.</p>
                    </div>
                </div>

                <div v-else class="row g-4">
                    <div v-for="test in publishedTests" :key="test.id" class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden test-card bg-white">
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex align-items-center gap-2 mb-4">
                                    <span
                                        class="badge bg-soft-success text-success rounded-pill px-3 py-2 fw-medium border border-success border-opacity-25">
                                        <i class="fas fa-clock me-1"></i>{{ test.thoi_gian_toi_da }} mins
                                    </span>
                                    <span
                                        class="badge bg-soft-primary text-primary rounded-pill px-3 py-2 fw-medium border border-primary border-opacity-25">
                                        <i class="fas fa-star me-1"></i>Max {{ test.diem_tong_max }} pts
                                    </span>
                                </div>
                                <h5 class="card-title fw-bold mb-3 text-dark">{{ test.ten_bai_test }}</h5>
                                <p class="card-text text-muted small mb-4 flex-grow-1 lh-lg">
                                    {{ test.mo_ta || 'General English assessment to help build your perfect learning' }}
                                </p>
                                <div
                                    class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top border-light">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 32px; height: 32px;">
                                            <i class="fas fa-user text-secondary small"></i>
                                        </div>
                                        <span class="text-dark fw-medium small">{{ test.giao_vien?.name || 'Tutor'
                                            }}</span>
                                    </div>
                                    <button class="btn btn-primary btn-sm rounded-pill px-4 py-2 fw-semibold shadow-sm"
                                        @click="goToTest(test.id)">
                                        <i class="fas fa-play me-2"></i> Take Test
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Registration Section -->
        <section id="register-section" class="register-section py-5 bg-white">
            <div class="container py-lg-4">
                <div class="card border-0 shadow-lg rounded-5 overflow-hidden">
                    <div class="row g-0">
                        <div class="col-lg-6 p-5 p-xl-5 d-flex flex-column justify-content-center bg-white">
                            <span
                                class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill mb-3 fw-semibold align-self-start">Free
                                Consultation</span>
                            <h2 class="display-6 fw-bold mb-3 text-dark">Start Your Journey</h2>
                            <p class="text-muted mb-5 fs-5">Leave your details and get a personalized roadmap completely
                                free within 24 hours.</p>

                            <form @submit.prevent="navigateToRegister">
                                <div class="mb-4">
                                    <input type="text" placeholder="Full Name"
                                        class="form-control form-control-lg bg-light border-0 px-4 rounded-3 shadow-none">
                                </div>
                                <div class="mb-4">
                                    <input type="text" placeholder="Phone Number"
                                        class="form-control form-control-lg bg-light border-0 px-4 rounded-3 shadow-none">
                                </div>
                                <div class="mb-5">
                                    <select
                                        class="form-select form-select-lg bg-light border-0 px-4 rounded-3 text-muted shadow-none cursor-pointer">
                                        <option selected disabled>Select Course</option>
                                        <option>IELTS Preparation</option>
                                        <option>Business English</option>
                                        <option>Communication</option>
                                    </select>
                                </div>
                                <button type="submit"
                                    class="btn btn-primary btn-lg w-100 rounded-pill py-3 fw-bold shadow-sm hover-scale">
                                    Sign Up Now
                                </button>
                            </form>
                        </div>
                        <div class="col-lg-6 d-none d-lg-block position-relative">
                            <img src="https://images.unsplash.com/photo-1513258496099-48168024aec0" alt="Register"
                                class="img-fluid w-100 h-100 object-fit-cover">
                            <div class="position-absolute top-0 start-0 w-100 h-100 overlay-gradient"></div>
                            <div class="position-absolute bottom-0 start-0 p-5 text-white z-2">
                                <h3 class="fw-bold fs-3 mb-3 text-white lh-base">"Education is the most powerful weapon
                                    which you can use to change the world."</h3>
                                <p class="fs-6 opacity-75 mb-0 fw-medium">— Nelson Mandela</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
</template>

<style scoped>
/* Custom Fonts */
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

.home-page {
    font-family: 'Outfit', sans-serif;
}

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, #fff 0%, #f9f0f6 100%);
}

.text-gradient {
    background: linear-gradient(to right, #fc74dd, #ff9eeb);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.bg-soft-primary {
    background-color: rgba(252, 116, 221, 0.1) !important;
}

.shadow-xxl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15) !important;
}

.hover-scale {
    transition: transform 0.3s ease;
}

.hover-scale:hover {
    transform: scale(1.02);
}

/* Floating Cards Animation */
.floating-card {
    animation: float 6s ease-in-out infinite;
}

.c1 {
    animation-delay: 0s;
}

.c2 {
    animation-delay: 3s;
}

@keyframes float {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-15px);
    }
}

/* Feature Cards */
.transition-hover {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.transition-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, .1) !important;
}

/* Line Clamp */
.card-text {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Responsive adjustments not covered by Bootstrap */
@media (max-width: 991.98px) {
    .hero-image {
        margin-top: 3rem;
    }
}

/* Test Cards */
.test-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-top: 3px solid transparent;
}

.test-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, .1) !important;
    border-top-color: #fc74dd;
}

.bg-soft-success {
    background-color: rgba(76, 175, 80, 0.1) !important;
}

.bg-soft-primary {
    background-color: rgba(252, 116, 221, 0.1) !important;
}

.overlay-gradient {
    background: linear-gradient(135deg, rgba(252, 116, 221, 0.6) 0%, rgba(33, 150, 243, 0.4) 100%);
}

.border-dashed {
    border-style: dashed !important;
}
</style>
