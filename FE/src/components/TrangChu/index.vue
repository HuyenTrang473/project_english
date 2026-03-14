<script setup>
import { ref, onMounted } from 'vue';
import { getLessons } from '@/api/lessonApi';
import { getListByLesson } from '@/api/testApi';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const courses = ref([]);
const loading = ref(true);
const featuredTestId = ref(null);

const resolveFeaturedTestId = async (lessons) => {
    for (const lesson of lessons) {
        const testRes = await getListByLesson(lesson.id);
        const tests = testRes.data || [];
        if (tests.length > 0) {
            featuredTestId.value = tests[0].id;
            return;
        }
    }
};

const fetchCourses = async () => {
    loading.value = true;
    try {
        const res = await getLessons();
        courses.value = res.data || [];
        await resolveFeaturedTestId(courses.value);
    } catch (error) {
        console.error("Failed to load courses", error);
    } finally {
        loading.value = false;
    }
};

const goToFreeTest = () => {
    if (!authStore.isAuthenticated) {
        router.push({ name: 'Login', query: { redirect: featuredTestId.value ? `/test/${featuredTestId.value}` : '/test' } });
        return;
    }

    if (authStore.userRole !== 'hoc_sinh') {
        alert('Chỉ tài khoản học sinh mới có thể vào trang thi thử.');
        router.push('/');
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
                            <h1 class="hero-title display-3 fw-bold mb-4 text-dark">
                                Master English with <span class="highlight text-gradient">Confidence</span>
                            </h1>
                            <p class="hero-subtitle lead text-secondary mb-5">
                                Join thousands of students achieving their goals with our premium IELTS and
                                communication courses.
                                Start your journey to fluency today.
                            </p>
                            <div class="hero-actions d-flex flex-wrap gap-3">
                                <button class="btn btn-primary btn-lg rounded-pill px-5 py-3 shadow-lg hover-lift"
                                    @click="scrollToRegister">
                                    Start Learning Now
                                </button>
                                <button class="btn btn-outline-dark btn-lg rounded-pill px-5 py-3 hover-lift"
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
                            <div
                                class="floating-card c1 bg-white p-3 rounded-4 shadow-lg position-absolute top-0 start-0 mt-4 ms-n4 z-2 border border-light">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-square bg-primary text-white p-2 rounded-3">
                                        <i class="fas fa-graduation-cap fa-lg"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 small text-muted">Target</p>
                                        <p class="mb-0 fw-bold text-dark">IELTS 8.0+</p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="floating-card c2 bg-white p-3 rounded-4 shadow-lg position-absolute bottom-0 end-0 mb-4 me-n4 z-2 border border-light">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-square bg-success text-white p-2 rounded-3">
                                        <i class="fas fa-fire fa-lg"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 small text-muted">Result</p>
                                        <p class="mb-0 fw-bold text-dark">Fast Track</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Row -->
                <div class="row mt-5 pt-5 border-top border-light">
                    <div class="col-6 col-md-4">
                        <div class="d-flex align-items-center gap-3">
                            <h3 class="fw-bolder mb-0 display-6 text-primary">10k+</h3>
                            <p class="mb-0 text-muted small lh-sm">Active<br>Students</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="d-flex align-items-center gap-3">
                            <h3 class="fw-bolder mb-0 display-6 text-primary">98%</h3>
                            <p class="mb-0 text-muted small lh-sm">Success<br>Rate</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 mt-4 mt-md-0">
                        <div class="d-flex align-items-center gap-3">
                            <h3 class="fw-bolder mb-0 display-6 text-primary">50+</h3>
                            <p class="mb-0 text-muted small lh-sm">Expert<br>Tutors</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section py-5 position-relative" style="z-index: 2;">
            <div class="container">
                <div class="row g-4 justify-content-center">
                    <div class="col-md-4">
                        <div class="feature-card bg-white p-4 rounded-4 shadow-sm h-100 transition-hover">
                            <div class="icon-box i-blue mb-3 rounded-3 d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px; background: #e3f2fd; color: #2196f3; font-size: 1.25rem;">
                                <i class="fa-solid fa-earth-americas"></i>
                            </div>
                            <h3 class="h5 fw-bold">IELTS Prep</h3>
                            <p class="text-muted small">Comprehensive verified curriculum for reading, writing,
                                listening, and speaking.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card bg-white p-4 rounded-4 shadow-sm h-100 transition-hover">
                            <div class="icon-box i-pink mb-3 rounded-3 d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px; background: #fff0fa; color: #fc74dd; font-size: 1.25rem;">
                                <i class="fa-solid fa-comments"></i>
                            </div>
                            <h3 class="h5 fw-bold">Communication</h3>
                            <p class="text-muted small">Practice real-world conversations with native speakers and
                                expert tutors.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card bg-white p-4 rounded-4 shadow-sm h-100 transition-hover">
                            <div class="icon-box i-green mb-3 rounded-3 d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px; background: #e8f5e9; color: #4caf50; font-size: 1.25rem;">
                                <i class="fa-solid fa-certificate"></i>
                            </div>
                            <h3 class="h5 fw-bold">Certification</h3>
                            <p class="text-muted small">Get recognized certificates upon completion of each level.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section class="about-section py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <h2 class="display-6 fw-bold mb-3">Why Choose Us?</h2>
                        <p class="lead text-muted mb-4">We combine technology with expert pedagogy to deliver the most
                            effective learning experience.</p>
                        <ul class="list-unstyled">
                            <li class="mb-3 d-flex align-items-center gap-2"><i
                                    class="fa-solid fa-check text-primary bg-light p-1 rounded-circle small"></i>
                                Personalized Learning Path</li>
                            <li class="mb-3 d-flex align-items-center gap-2"><i
                                    class="fa-solid fa-check text-primary bg-light p-1 rounded-circle small"></i> 24/7
                                Access to Materials</li>
                            <li class="mb-3 d-flex align-items-center gap-2"><i
                                    class="fa-solid fa-check text-primary bg-light p-1 rounded-circle small"></i> Live
                                Interactive Sessions</li>
                        </ul>
                    </div>
                    <div class="col-lg-6">
                        <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644" alt="Team"
                            class="img-fluid rounded-4 shadow">
                    </div>
                </div>
            </div>
        </section>

        <!-- Courses Section -->
        <section class="courses-section py-5 bg-white">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <h2 class="h2 fw-bold mb-0">Our Popular Courses</h2>
                    <a href="#" class="btn btn-link text-decoration-none">View All Courses <i
                            class="fa-solid fa-arrow-right list-inline-item"></i></a>
                </div>

                <div v-if="loading" class="text-center py-5 text-muted">Loading courses...</div>

                <div v-else class="row g-4">
                    <div v-for="(course, index) in courses" :key="index" class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden course-card">
                            <div class="position-relative">
                                <img :src="course.hinh_anh" class="card-img-top object-fit-cover" alt="Course Image"
                                    style="height: 200px;">
                                <span class="position-absolute top-0 end-0 m-3 badge bg-primary rounded-pill">Hot</span>
                            </div>
                            <div class="card-body p-4">
                                <h3 class="card-title h5 fw-bold">{{ course.title }}</h3>
                                <p class="card-text text-muted small">{{ course.description }}</p>
                                <span class="badge text-primary bg-light mb-3">Teacher: {{ course.teacher?.name
                                    }}</span>
                                <button class="btn btn-outline-primary w-100 rounded-3 mt-auto"
                                    @click="navigateToRegister">Register Now</button>
                            </div>
                        </div>
                    </div>
                    <!-- Mock Data if empty -->
                    <div v-if="courses.length === 0" class="col-12 text-center text-muted">
                        <p>No courses available right now. Check back later!</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Registration Section -->
        <section id="register-section" class="register-section py-5 bg-light">
            <div class="container">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="row g-0">
                        <div class="col-lg-6 p-5">
                            <h2 class="fw-bold mb-3">Start Your Journey</h2>
                            <p class="text-muted mb-4">Get a free consultation and roadmap within 24 hours.</p>

                            <form @submit.prevent="navigateToRegister">
                                <div class="mb-3">
                                    <input type="text" placeholder="Full Name"
                                        class="form-control form-control-lg bg-light border-0">
                                </div>
                                <div class="mb-3">
                                    <input type="text" placeholder="Phone Number"
                                        class="form-control form-control-lg bg-light border-0">
                                </div>
                                <div class="mb-4">
                                    <select class="form-select form-select-lg bg-light border-0 text-muted">
                                        <option selected disabled>Select Course</option>
                                        <option>IELTS Preparation</option>
                                        <option>Business English</option>
                                        <option>Communication</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill">Sign Up
                                    Now</button>
                            </form>
                        </div>
                        <div class="col-lg-6 d-none d-lg-block position-relative">
                            <img src="https://images.unsplash.com/photo-1513258496099-48168024aec0" alt="Register"
                                class="img-fluid w-100 h-100 object-fit-cover">
                            <div class="position-absolute top-0 start-0 w-100 h-100"
                                style="background: linear-gradient(to right, rgba(252, 116, 221, 0.2), transparent);">
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
</style>
