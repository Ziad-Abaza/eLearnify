import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import Home from '../views/Home.vue';
import Login from '../views/auth/LoginView.vue';
import Register from '../views/auth/RegisterView.vue';
import Dashboard from '../views/DashboardView.vue';
import UserManagement from '../views/admin/UserManagement.vue';
import RoleManagement from '../views/admin/RoleManagementView.vue';

// Instructor Components
import InstructorLayout from '../views/instructor/InstructorLayout.vue';
import InstructorCoursesList from '../views/instructor/InstructorCoursesList.vue';
import InstructorCourseForm from '../views/instructor/InstructorCourseForm.vue'; // Import actual component

const routes = [{
        path: '/',
         name: 'home',
        component: Home,
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { guest: true }
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: { guest: true }
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard,
        meta: { requiresAuth: true }
    },
    {
        path: '/admin/users',
        name: 'user-management',
        component: UserManagement,
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
        path: '/admin/roles',
        name: 'role-management',
        component: RoleManagement,
        meta: { requiresAuth: true, requiresAdmin: true }
    },
    // Instructor Routes
    {
        path: '/instructor',
        component: InstructorLayout, // Using a layout for instructor section
        meta: { requiresAuth: true, requiresInstructor: true },
        children: [{
                path: '',
                redirect: '/instructor/courses' // Default to courses list
            },
            {
                path: 'courses',
                name: 'instructor-courses',
                component: InstructorCoursesList
            },
            {
                path: 'courses/create',
                name: 'instructor-course-create',
                component: InstructorCourseForm
            },
            {
                path: 'courses/:courseId/edit',
                name: 'instructor-course-edit',
                component: InstructorCourseForm,
                props: true
            }
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach(async(to, from, next) => {
    const authStore = useAuthStore();
    const isAuthenticated = authStore.isAuthenticated;
    const isAdmin = authStore.isAdmin;
    const isInstructor = authStore.isInstructor; // Get instructor status

    // Routes that require authentication
    if (to.meta.requiresAuth) {
        if (!isAuthenticated) {
            next({ name: 'login' });
            return;
        }

        // Check for admin routes
        if (to.meta.requiresAdmin && !isAdmin) {
            next({ name: 'dashboard' }); // Or a 403 page
            return;
        }

        // Check for instructor routes
        if (to.meta.requiresInstructor && !isInstructor) {
            next({ name: 'dashboard' }); // Or a 403 page
            return;
        }
    }

    // Guest routes (accessible only when not authenticated)
    if (to.meta.guest) {
        if (isAuthenticated) {
            next({ name: 'dashboard' });
            return;
        }
    }

    next();
});

export default router;