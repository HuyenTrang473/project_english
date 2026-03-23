<template>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                <h4>🎓 Admin Panel</h4>
            </div>
            <nav class="sidebar-nav">
                <a v-for="item in menuItems" :key="item.key" href="#" class="nav-link"
                    :class="{ active: activeMenu === item.key }" @click.prevent="handleMenuClick(item)">
                    <span class="nav-icon">{{ item.icon }}</span>
                    {{ item.label }}
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="admin-main">
            <header class="admin-header">
                <h2>{{ currentTitle }}</h2>
            </header>

            <div class="admin-content">
                <!-- Dashboard -->
                <div v-if="activeMenu === 'dashboard'" class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon bg-primary">📚</div>
                        <div class="stat-info">
                            <span class="stat-value">{{ stats.lessons }}</span>
                            <span class="stat-label">Bài học</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon bg-success">📝</div>
                        <div class="stat-info">
                            <span class="stat-value">{{ stats.tests }}</span>
                            <span class="stat-label">Đề thi</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon bg-info">👩‍🏫</div>
                        <div class="stat-info">
                            <span class="stat-value">{{ stats.teachers }}</span>
                            <span class="stat-label">Giáo viên</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon bg-warning">👨‍🎓</div>
                        <div class="stat-info">
                            <span class="stat-value">{{ stats.students }}</span>
                            <span class="stat-label">Học sinh</span>
                        </div>
                    </div>
                </div>

                <!-- Quản lý Bài học -->
                <div v-else-if="activeMenu === 'lessons'" class="placeholder-section">
                    <h4>Quản lý Bài học</h4>
                    <p>Chức năng đang phát triển...</p>
                </div>

                <!-- Quản lý Đề thi -->
                <AdminTestManager v-else-if="activeMenu === 'tests'" />

                <!-- Quản lý Giáo viên -->
                <AdminTeacherManager v-else-if="activeMenu === 'teachers'" />

                <!-- Thống kê -->
                <div v-else-if="activeMenu === 'stats'" class="placeholder-section">
                    <h4>Thống kê</h4>
                    <p>Chức năng đang phát triển...</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import http from '@/api/axiosClient';
import AdminTestManager from './AdminTestManager.vue';
import AdminTeacherManager from './AdminTeacherManager.vue';

export default {
    components: {
        AdminTestManager,
        AdminTeacherManager,
    },
    setup() {
        const router = useRouter();
        const authStore = useAuthStore();
        return { router, authStore };
    },
    data() {
        return {
            activeMenu: 'dashboard',
            allMenuItems: [
                { key: 'dashboard', label: 'Dashboard', icon: '🏠', route: null, adminOnly: false },
                { key: 'lessons', label: 'Quản lý Bài học', icon: '📚', route: '/lessons', adminOnly: false },
                { key: 'tests', label: 'Quản lý Đề thi', icon: '📝', route: '/tests', adminOnly: false },
                { key: 'teachers', label: 'Quản lý Giáo viên', icon: '👨‍🏫', route: null, adminOnly: true },
                { key: 'stats', label: 'Thống kê', icon: '📊', route: null, adminOnly: false },
            ],
            stats: {
                lessons: 0,
                tests: 0,
                teachers: 0,
                students: 0,
            },
        };
    },
    computed: {
        menuItems() {
            // Filter menu items based on user role
            return this.allMenuItems.filter((item) => {
                if (item.adminOnly) {
                    return this.authStore.isAdmin;
                }
                return true;
            });
        },
        currentTitle() {
            const item = this.menuItems.find((m) => m.key === this.activeMenu);
            return item ? item.label : 'Dashboard';
        },
    },
    mounted() {
        this.loadStats();
        // Ensure teachers can't manually access teacher management
        if (this.activeMenu === 'teachers' && !this.authStore.isAdmin) {
            this.activeMenu = 'dashboard';
        }
    },
    methods: {
        handleMenuClick(item) {
            // Check if user has permission to access this menu
            if (item.adminOnly && !this.authStore.isAdmin) {
                alert('Bạn không có quyền truy cập mục này');
                return;
            }
            this.activeMenu = item.key;
            // Navigate to route if exists
            if (item.route) {
                this.router.push(item.route);
            }
        },
        async loadStats() {
            try {
                const res = await http.get('/admin/stats');
                if (res.data) {
                    this.stats = { ...this.stats, ...res.data };
                }
            } catch (error) {
                // Stats API may not exist yet — show zeros
                console.error('Error loading stats:', error);
            }
        },
    },
};
</script>

<style scoped>
.admin-wrapper {
    display: flex;
    min-height: 100vh;
    background: #f0f2f5;
}

/* Sidebar */
.admin-sidebar {
    width: 260px;
    background: #1e293b;
    color: #fff;
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
}

.sidebar-brand {
    padding: 1.5rem 1.25rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-brand h4 {
    margin: 0;
    font-size: 1.15rem;
}

.sidebar-nav {
    display: flex;
    flex-direction: column;
    padding: 0.75rem 0;
}

.sidebar-nav .nav-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1.25rem;
    color: #94a3b8;
    text-decoration: none;
    font-size: 0.95rem;
    transition: all 0.2s;
}

.sidebar-nav .nav-link:hover {
    color: #fff;
    background: rgba(255, 255, 255, 0.05);
}

.sidebar-nav .nav-link.active {
    color: #fff;
    background: rgba(255, 255, 255, 0.1);
    border-left: 3px solid #3b82f6;
}

.nav-icon {
    font-size: 1.1rem;
}

/* Main */
.admin-main {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.admin-header {
    background: #fff;
    padding: 1.25rem 2rem;
    border-bottom: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.admin-header h2 {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 600;
}

.admin-content {
    padding: 2rem;
}

/* Stat Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1.5rem;
}

.stat-card {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.bg-primary {
    background: #dbeafe;
}

.bg-success {
    background: #dcfce7;
}

.bg-info {
    background: #e0f2fe;
}

.bg-warning {
    background: #fef9c3;
}

.stat-info {
    display: flex;
    flex-direction: column;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1e293b;
}

.stat-label {
    font-size: 0.85rem;
    color: #64748b;
}

/* Placeholder */
.placeholder-section {
    background: #fff;
    border-radius: 12px;
    padding: 3rem;
    text-align: center;
    color: #64748b;
}

.placeholder-section h4 {
    margin-bottom: 0.5rem;
    color: #1e293b;
}

/* Responsive */
@media (max-width: 768px) {
    .admin-wrapper {
        flex-direction: column;
    }

    .admin-sidebar {
        width: 100%;
    }

    .sidebar-nav {
        flex-direction: row;
        overflow-x: auto;
    }

    .sidebar-nav .nav-link {
        white-space: nowrap;
    }

    .sidebar-nav .nav-link.active {
        border-left: none;
        border-bottom: 3px solid #3b82f6;
    }
}
</style>