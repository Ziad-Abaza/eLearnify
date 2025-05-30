<template>
    <div id="app">
        <nav v-if="authStore.isAuthenticated" class="bg-white shadow sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#f0f2f5] px-10 py-3">
                    <div class="flex items-center gap-4 text-[#111418]">
                        <div class="size-4">
                            <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M24 4C25.7818 14.2173 33.7827 22.2182 44 24C33.7827 25.7818 25.7818 33.7827 24 44C22.2182 33.7827 14.2173 25.7818 4 24C14.2173 22.2182 22.2182 14.2173 24 4Z"
                                    fill="currentColor"
                                ></path>
                            </svg>
                        </div>
                        <h2 class="text-[#111418] text-lg font-bold leading-tight tracking-[-0.015em]">EduStream</h2>
                    </div>
                    <div class="flex flex-1 justify-end gap-8">
                        <div class="flex items-center gap-9">
                            <router-link class="text-[#111418] text-sm font-medium leading-normal" to="/dashboard">Home</router-link>
                            <router-link class="text-[#111418] text-sm font-medium leading-normal" to="/courses">Courses</router-link>
                            <router-link class="text-[#111418] text-sm font-medium leading-normal" to="/about">About Us</router-link>
                            <router-link class="text-[#111418] text-sm font-medium leading-normal" to="/contact">Contact</router-link>
                        </div>
                        <button
                            class="flex max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 bg-[#f0f2f5] text-[#111418] gap-2 text-sm font-bold leading-normal tracking-[0.015em] min-w-0 px-2.5"
                        >
                            <div class="text-[#111418]" data-icon="User" data-size="20px" data-weight="regular">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                                    <path
                                        d="M230.92,212c-15.23-26.33-38.7-45.21-66.09-54.16a72,72,0,1,0-73.66,0C63.78,166.78,40.31,185.66,25.08,212a8,8,0,1,0,13.85,8c18.84-32.56,52.14-52,89.07-52s70.23,19.44,89.07,52a8,8,0,1,0,13.85-8ZM72,96a56,56,0,1,1,56,56A56.06,56.06,0,0,1,72,96Z"
                                    ></path>
                                </svg>
                            </div>
                        </button>
                        <div
                            class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-10"
                            style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCt36veR3nVgqjmQZM-0WF2SzanZSAqk2KlPnWA-oOcp41CZkcawgNC8cRXrdyQep9L1IjjDUzlg3aW1j8Mx7PLg--sFqE6ieOAfyerE7xdvN7y9zOTvExbRgRx-D2UvkFE-nG6Z3sZH0S50jEv_Ubuj2wg08SWJMlSmDRMRS0kwI0TKFUUho0OQ3hSRHAjGmBNtbvO8sLrdud6tqdbbHOO1z0vhPz2TzuffbiNhMfeGwSF8WUZhrk9Bnf6rPwmWGj3Sd3k31gmFDM");'
                        ></div>
                    </div>
                </header>
            </div>
            <!-- Mobile Nav -->
            <div v-if="mobileOpen" class="md:hidden bg-white shadow-lg border-t border-gray-100">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <router-link to="/dashboard" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition flex items-center gap-2" active-class="bg-blue-100 text-blue-700">
                        <font-awesome-icon icon="fa-solid fa-house" />
                        Dashboard
                    </router-link>
                    <router-link v-if="authStore.isAdmin" to="/admin/users" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition flex items-center gap-2" active-class="bg-blue-100 text-blue-700">
                        <font-awesome-icon icon="fa-solid fa-users" />
                        User Management
                    </router-link>
                    <router-link v-if="authStore.isAdmin" to="/admin/roles" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition flex items-center gap-2" active-class="bg-blue-100 text-blue-700">
                        <font-awesome-icon icon="fa-solid fa-user-shield" />
                        Role Management
                    </router-link>
                    <router-link v-if="authStore.isInstructor" to="/instructor/courses" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition flex items-center gap-2" active-class="bg-blue-100 text-blue-700">
                        <font-awesome-icon icon="fa-solid fa-chalkboard-teacher" />
                        Instructor Dashboard
                    </router-link>
                    <button @click="handleLogout" class="w-full mt-2 px-3 py-2 rounded-md text-base font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow transition flex items-center gap-2">
                        <font-awesome-icon icon="fa-solid fa-right-from-bracket" />
                        Logout
                    </button>
                </div>
            </div>
        </nav>
        <div class="container mx-auto px-4">
            <router-view></router-view>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from './stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();
const mobileOpen = ref(false);

const handleLogout = async () => {
    mobileOpen.value = false;
    await authStore.logout();
    router.push({ name: 'login' });
};

// If the app is loaded and there's a token, try to fetch user to update roles, etc.
// This is especially useful if the user refreshes the page.
if (authStore.token && !authStore.user) {
    authStore.fetchUser().catch(err => {
        console.error("Failed to fetch user on app load, logging out if token invalid.");
        // If fetchUser fails (e.g. token expired), logout might be appropriate
        // authStore.logout();
        // router.push({ name: 'login' });
    });
}

</script>

<style>
/* Global styles if any - consider using a dedicated CSS file */
body {
    background-color: #f7fafc; /* Tailwind gray-100 */
}
</style>
