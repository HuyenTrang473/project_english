<script setup>
import { ref, onMounted, computed } from 'vue';
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
const animateCounters = ref(false);
const statValues = ref({ students: 0, success: 0, tutors: 0 });

const visibleCourses = computed(() => courses.value.slice(0, 3));
const visibleTests = computed(() => publishedTests.value.slice(0, 3));

const animateValue = (start, end, duration) => {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        return Math.floor(progress * (end - start) + start);
    };
    return step;
};

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

const goToCourseDetail = (courseId) => {
    if (!courseId) {
        return;
    }
    router.push({
        path: `/lessons/${courseId}`,
        query: { from: 'home' },
    });
};

const handleImageError = (event) => {
    event.target.src = 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?w=500';
};

onMounted(() => {
    fetchCourses();
    observeScrollElements();
    observeStatsSection();
});

const observeScrollElements = () => {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.scroll-animate').forEach(el => {
        observer.observe(el);
    });
};

const observeStatsSection = () => {
    const statsSection = document.querySelector('.stats-row');
    if (!statsSection) return;

    const observerOptions = { threshold: 0.5 };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !animateCounters.value) {
                animateCounters.value = true;
                animateCounter('.stat-students', 10000, 2000);
                animateCounter('.stat-success', 98, 2000);
                animateCounter('.stat-tutors', 50, 2000);
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    observer.observe(statsSection);
};

const animateCounter = (selector, target, duration) => {
    const element = document.querySelector(selector);
    if (!element) return;

    let current = 0;
    const increment = target / (duration / 16);
    const interval = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target.toLocaleString() + (selector === '.stat-success' ? '%' : '+');
            clearInterval(interval);
        } else {
            element.textContent = Math.floor(current).toLocaleString() + (selector === '.stat-success' ? '%' : '+');
        }
    }, 16);
};
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
                <div class="stats-row row mt-5 pt-5 border-top border-light opacity-75">
                    <div class="col-6 col-md-4 scroll-animate fade-up">
                        <div class="d-flex align-items-center gap-3">
                            <h3 class="stat-students fw-bolder mb-0 display-6 text-primary">0+</h3>
                            <p class="mb-0 text-muted small lh-sm fw-medium">Active<br>Students</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 scroll-animate fade-up">
                        <div class="d-flex align-items-center gap-3">
                            <h3 class="stat-success fw-bolder mb-0 display-6 text-primary">0%</h3>
                            <p class="mb-0 text-muted small lh-sm fw-medium">Success<br>Rate</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 mt-4 mt-md-0 scroll-animate fade-up">
                        <div class="d-flex align-items-center gap-3">
                            <h3 class="stat-tutors fw-bolder mb-0 display-6 text-primary">0+</h3>
                            <p class="mb-0 text-muted small lh-sm fw-medium">Expert<br>Tutors</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section py-5 position-relative bg-light" style="z-index: 2;">
            <div class="container py-4">
                <div class="text-center mb-5 scroll-animate fade-up">
                    <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill mb-2 fw-semibold">Outstanding
                        Features</span>
                    <h2 class="h2 fw-bold text-dark mb-0">Comprehensive Learning Ecosystem</h2>
                </div>
                <div class="row g-4 justify-content-center">
                    <div class="col-md-4 scroll-animate fade-up">
                        <div
                            class="feature-card bg-white p-4 rounded-4 shadow-sm h-100 transition-hover border-0 glassmorphic">
                            <div class="icon-box i-blue mb-4 rounded-3 d-flex align-items-center justify-content-center icon-animate"
                                style="width: 56px; height: 56px; background: #e3f2fd; color: #2196f3; font-size: 1.5rem;">
                                <i class="fa-solid fa-earth-americas"></i>
                            </div>
                            <h3 class="h5 fw-bold mb-3 text-dark">IELTS Prep</h3>
                            <p class="text-muted small mb-0 lh-lg">Comprehensive verified curriculum for reading,
                                writing, listening, and speaking.</p>
                        </div>
                    </div>
                    <div class="col-md-4 scroll-animate fade-up" style="animation-delay: 0.1s;">
                        <div
                            class="feature-card bg-white p-4 rounded-4 shadow-sm h-100 transition-hover border-0 glassmorphic">
                            <div class="icon-box i-pink mb-4 rounded-3 d-flex align-items-center justify-content-center icon-animate"
                                style="width: 56px; height: 56px; background: #fff0fa; color: #fc74dd; font-size: 1.5rem;">
                                <i class="fa-solid fa-comments"></i>
                            </div>
                            <h3 class="h5 fw-bold mb-3 text-dark">Communication</h3>
                            <p class="text-muted small mb-0 lh-lg">Practice real-world conversations with native
                                speakers and expert tutors.</p>
                        </div>
                    </div>
                    <div class="col-md-4 scroll-animate fade-up" style="animation-delay: 0.2s;">
                        <div
                            class="feature-card bg-white p-4 rounded-4 shadow-sm h-100 transition-hover border-0 glassmorphic">
                            <div class="icon-box i-green mb-4 rounded-3 d-flex align-items-center justify-content-center icon-animate"
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
                    <div class="col-lg-6 mb-5 mb-lg-0 scroll-animate fade-left">
                        <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill mb-3 fw-semibold">About
                            Us</span>
                        <h2 class="display-6 fw-bold mb-4 lh-base text-dark">Why Choose Us?</h2>
                        <p class="lead text-muted mb-5">We combine technology with expert pedagogy to deliver the most
                            effective learning experience.</p>
                        <ul class="list-unstyled">
                            <li class="mb-4 d-flex align-items-center gap-3 scroll-animate fade-left"
                                style="animation-delay: 0.2s;">
                                <div class="icon-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center rounded-circle"
                                    style="width: 40px; height: 40px;">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <span class="fw-medium text-dark">Personalized Learning Path</span>
                            </li>
                            <li class="mb-4 d-flex align-items-center gap-3 scroll-animate fade-left"
                                style="animation-delay: 0.35s;">
                                <div class="icon-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center rounded-circle"
                                    style="width: 40px; height: 40px;">
                                    <i class="fa-solid fa-book-open"></i>
                                </div>
                                <span class="fw-medium text-dark">24/7 Access to Materials</span>
                            </li>
                            <li class="mb-0 d-flex align-items-center gap-3 scroll-animate fade-left"
                                style="animation-delay: 0.5s;">
                                <div class="icon-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center rounded-circle"
                                    style="width: 40px; height: 40px;">
                                    <i class="fa-solid fa-video"></i>
                                </div>
                                <span class="fw-medium text-dark">Live Interactive Sessions</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-6 position-relative scroll-animate fade-right">
                        <div class="position-absolute w-100 h-100 bg-soft-primary rounded-4 floating-bg"
                            style="top: 20px; left: -20px; z-index: -1;"></div>
                        <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644" alt="Team"
                            class="img-fluid rounded-4 shadow-sm object-fit-cover zoom-on-hover"
                            style="height: 500px; width: 100%;">
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
                    <div v-for="(course, index) in visibleCourses" :key="index"
                        class="col-md-6 col-lg-4 scroll-animate fade-up"
                        :style="{ 'animation-delay': `${index * 0.1}s` }">
                        <div
                            class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden course-card transition-hover glassmorphic">
                            <div class="position-relative overflow-hidden">
                                <img :src="course.hinh_anh || 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?w=500'"
                                    class="card-img-top object-fit-cover zoom-on-hover" alt="Course Image"
                                    style="height: 220px;" @error="handleImageError">
                                <span
                                    class="position-absolute top-0 end-0 m-3 badge bg-primary bg-gradient rounded-pill px-3 py-2 shadow-sm fw-medium pulse-badge">Hot</span>
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
                                    @click="goToCourseDetail(course.id)">View Course</button>
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
                    <div v-for="test in visibleTests" :key="test.id" class="col-md-6 col-lg-4 scroll-animate fade-up"
                        :style="{ 'animation-delay': `${visibleTests.indexOf(test) * 0.1}s` }">
                        <div
                            class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden test-card bg-white glassmorphic">
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
        <section id="register-section" class="register-section py-5 bg-white scroll-animate fade-up">
            <div class="container py-lg-4">
                <div class="card border-0 shadow-lg rounded-5 overflow-hidden glassmorphic-card">
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
    overflow-x: hidden;
}

/* ===== SCROLL ANIMATIONS ===== */
.scroll-animate {
    opacity: 0;
}

.scroll-animate.animate-in {
    animation: fadeInScroll 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
}

.scroll-animate.fade-up {
    animation: slideUpFadeSmooth 1s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
}

.scroll-animate.fade-left {
    animation: slideLeftFadeSmooth 1s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
}

.scroll-animate.fade-right {
    animation: slideRightFadeSmooth 1s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
}

@keyframes fadeInScroll {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

@keyframes slideUpFadeSmooth {
    0% {
        opacity: 0;
        transform: translateY(50px);
    }

    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideLeftFadeSmooth {
    0% {
        opacity: 0;
        transform: translateX(-50px);
    }

    100% {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideRightFadeSmooth {
    0% {
        opacity: 0;
        transform: translateX(50px);
    }

    100% {
        opacity: 1;
        transform: translateX(0);
    }
}

/* ===== GLASSMORPHISM ===== */
.glassmorphic,
.glassmorphic-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
}

.glassmorphic:hover,
.glassmorphic-card:hover {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(15px);
}

/* ===== ICON ANIMATIONS ===== */
.icon-animate {
    transition: all 0.3s ease;
    animation: iconBounce 2s ease-in-out infinite;
}

@keyframes iconBounce {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-8px);
    }
}

.feature-card:hover .icon-animate {
    animation: iconPulse 0.6s ease-in-out;
}

@keyframes iconPulse {
    0% {
        transform: scale(1) translateY(0);
    }

    50% {
        transform: scale(1.15) translateY(-5px);
    }

    100% {
        transform: scale(1) translateY(0);
    }
}

/* ===== PULSE EFFECT ===== */
.pulse-badge {
    animation: badgePulse 2s ease-in-out infinite;
}

@keyframes badgePulse {

    0%,
    100% {
        box-shadow: 0 0 0 0 rgba(252, 116, 221, 0.7);
    }

    50% {
        box-shadow: 0 0 0 10px rgba(252, 116, 221, 0);
    }
}

/* ===== ZOOM ON HOVER ===== */
.zoom-on-hover {
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.zoom-on-hover:hover {
    transform: scale(1.05);
}

/* ===== FLOATING BACKGROUND ===== */
.floating-bg {
    animation: floatBackgroundSmooth 10s cubic-bezier(0.4, 0.0, 0.2, 1) infinite;
}

@keyframes floatBackgroundSmooth {

    0%,
    100% {
        transform: translateY(0px) translateX(0px);
    }

    50% {
        transform: translateY(-15px) translateX(5px);
    }
}

/* ===== GRADIENT ANIMATIONS ===== */
.hero-section {
    background: linear-gradient(135deg, #fff 0%, #f9f0f6 100%);
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(252, 116, 221, 0.1) 1px, transparent 1px);
    background-size: 50px 50px;
    animation: gridMove 20s linear infinite;
    z-index: 0;
}

@keyframes gridMove {
    0% {
        transform: translate(0, 0);
    }

    100% {
        transform: translate(50px, 50px);
    }
}

/* ===== UPDATED STYLES ===== */

.text-gradient {
    background: linear-gradient(to right, #fc74dd, #ff9eeb);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 700;
}

.bg-soft-primary {
    background-color: rgba(252, 116, 221, 0.1) !important;
}

.bg-soft-success {
    background-color: rgba(76, 175, 80, 0.1) !important;
}

.shadow-xxl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
}

.hover-lift {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
    position: relative;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2) !important;
}

.hover-scale {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hover-scale:hover {
    transform: scale(1.05);
}

/* Floating Cards Animation */
.floating-card {
    animation: floatSmooth 8s cubic-bezier(0.4, 0.0, 0.2, 1) infinite;
}

.c1 {
    animation-delay: 0.8s;
}

.c2 {
    animation-delay: 1.5s;
}

@keyframes floatSmooth {

    0%,
    100% {
        transform: translateY(0) rotate(0deg);
    }

    25% {
        transform: translateY(-12px) rotate(1deg);
    }

    50% {
        transform: translateY(-20px) rotate(0deg);
    }

    75% {
        transform: translateY(-12px) rotate(-1deg);
    }
}

/* Feature Cards */
.transition-hover {
    transition: all 0.4s cubic-bezier(0.23, 1, 0.320, 1);
}

.transition-hover:hover {
    transform: translateY(-12px);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15) !important;
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
    transition: all 0.4s cubic-bezier(0.23, 1, 0.320, 1);
    border-top: 3px solid transparent;
    position: relative;
}

.test-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, #fc74dd, #ff9eeb, #fc74dd);
    opacity: 0;
    transition: opacity 0.4s cubic-bezier(0.23, 1, 0.320, 1);
}

.test-card:hover::before {
    opacity: 1;
}

.test-card:hover {
    transform: translateY(-12px);
    box-shadow: 0 25px 50px rgba(252, 116, 221, 0.25) !important;
    border-top-color: #fc74dd;
}

.overlay-gradient {
    background: linear-gradient(135deg, rgba(252, 116, 221, 0.6) 0%, rgba(33, 150, 243, 0.4) 100%);
}

.border-dashed {
    border-style: dashed !important;
}

/* Course Cards Enhancement */
.course-card {
    transition: all 0.4s cubic-bezier(0.23, 1, 0.320, 1);
}

.course-card:hover {
    transform: translateY(-12px);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15) !important;
}

/* Star Glow Animation */
.highlight {
    animation: highlightGlow 2s ease-in-out infinite;
}

@keyframes highlightGlow {

    0%,
    100% {
        text-shadow: 0 0 10px rgba(252, 116, 221, 0.3);
    }

    50% {
        text-shadow: 0 0 20px rgba(252, 116, 221, 0.6);
    }
}

/* Button Enhancement */
.btn {
    transition: all 0.4s cubic-bezier(0.23, 1, 0.320, 1);
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s cubic-bezier(0.23, 1, 0.320, 1), height 0.6s cubic-bezier(0.23, 1, 0.320, 1);
}

.btn:hover::before {
    width: 300px;
    height: 300px;
}

/* Section Parallax Effects */
.features-section {
    position: relative;
}

.features-section::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 100px;
    background: linear-gradient(to bottom, transparent, rgba(255, 255, 255, 0.5));
    pointer-events: none;
}

/* Text Animations */
.hero-title {
    animation: titleSlideIn 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) 0.2s backwards;
}

.hero-subtitle {
    animation: subtitleFadeIn 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) 0.4s backwards;
}

.hero-actions {
    animation: actionsSlideUp 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) 0.6s backwards;
}

.badge {
    animation: badgeSlideDown 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) 0.1s backwards;
}

@keyframes titleSlideIn {
    from {
        opacity: 0;
        transform: translateY(40px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes subtitleFadeIn {
    from {
        opacity: 0;
        transform: translateY(30px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes actionsSlideUp {
    from {
        opacity: 0;
        transform: translateY(25px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes badgeSlideDown {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Smooth Transitions for All */
* {
    scroll-behavior: smooth;
}

/* Input Fields Animation */
.form-control,
.form-select {
    transition: all 0.4s cubic-bezier(0.23, 1, 0.320, 1);
    border: 2px solid transparent !important;
}

.form-control:focus,
.form-select:focus {
    border-color: #fc74dd !important;
    box-shadow: 0 0 0 0.3rem rgba(252, 116, 221, 0.2) !important;
    transform: translateY(-3px);
}

/* Responsive Design Enhancements */
@media (max-width: 768px) {
    .hero-section::before {
        display: none;
    }

    .scroll-animate {
        animation-delay: 0s !important;
    }
}
</style>
