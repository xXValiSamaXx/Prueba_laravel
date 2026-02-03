<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter, RouterLink } from 'vue-router';

const route = useRoute();
const router = useRouter();
const isDarkMode = ref(false);

const navigation = [
    { name: 'Dashboard', href: '/dashboard', icon: 'dashboard' },
    { name: 'Pedidos', href: '/orders', icon: 'list_alt' },
    { name: 'Clientes', href: '/customers', icon: 'group' },
    { name: 'Productos', href: '/products', icon: 'inventory' },
    { name: 'Reportes', href: '/reports', icon: 'analytics' },
];

const checkDarkMode = () => {
    // Force light mode by default, only use dark if explicitly set
    if (localStorage.theme === 'dark') {
        document.documentElement.classList.add('dark');
        isDarkMode.value = true;
    } else {
        document.documentElement.classList.remove('dark');
        isDarkMode.value = false;
        // Set light as default if not set
        if (!localStorage.theme) {
            localStorage.theme = 'light';
        }
    }
};

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    localStorage.setItem('darkMode', isDarkMode.value);
    if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
        localStorage.theme = 'dark';
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.theme = 'light';
    }
};

const logout = () => {
    // Clear authentication token
    localStorage.removeItem('auth_token');
    // Redirect to login
    router.push('/login');
};

onMounted(() => {
    checkDarkMode();
});
</script>

<template>
    <div class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 hidden lg:flex flex-col bg-slate-900 dark:bg-slate-950 border-r border-slate-800 shrink-0">
            <div class="p-6 flex items-center gap-3">
                <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center text-white">
                    <span class="material-symbols-outlined">inventory_2</span>
                </div>
                <span class="font-bold text-lg text-white tracking-tight">OrderManager</span>
            </div>
            <nav class="flex-1 px-4 space-y-1 mt-4">
                <RouterLink 
                    v-for="item in navigation" 
                    :key="item.name" 
                    :to="item.href"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors"
                    :class="[
                        route.path === item.href 
                        ? 'bg-primary text-white' 
                        : 'text-slate-400 hover:text-white hover:bg-slate-800'
                    ]"
                >
                    <span class="material-symbols-outlined">{{ item.icon }}</span>
                    <span class="font-medium">{{ item.name }}</span>
                </RouterLink>
            </nav>
            <div class="p-4 border-t border-slate-800">
                <router-link to="/settings" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-colors">
                    <span class="material-symbols-outlined">settings</span>
                    <span class="font-medium">Configuración</span>
                </router-link>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Navbar -->
            <header class="h-16 flex items-center justify-between px-8 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 shrink-0">
                <div class="flex-1 max-w-lg">
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 group-focus-within:text-primary transition-colors">
                            <span class="material-symbols-outlined">search</span>
                        </span>
                        <input class="block w-full pl-10 pr-3 py-2 border-none bg-slate-100 dark:bg-slate-800 rounded-full text-sm focus:ring-2 focus:ring-primary transition-all" placeholder="Buscar..." type="text"/>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button 
                        @click="toggleDarkMode"
                        class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors"
                    >
                        <span class="material-symbols-outlined">{{ isDarkMode ? 'light_mode' : 'dark_mode' }}</span>
                    </button>
                    <div class="h-8 w-px bg-slate-200 dark:bg-slate-800 mx-2"></div>
                    <button class="relative p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
                        <span class="material-symbols-outlined">notifications</span>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white dark:border-slate-900"></span>
                    </button>
                    <div class="h-8 w-px bg-slate-200 dark:bg-slate-800 mx-2"></div>
                    
                    <!-- User Profile Dropdown -->
                    <div class="relative group">
                        <div class="flex items-center gap-3 px-3 py-2 rounded-lg cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            <div class="text-right">
                                <div class="text-sm font-semibold text-slate-900 dark:text-white">Admin Usuario</div>
                                <div class="text-xs text-slate-500">Super Admin</div>
                            </div>
                            <div class="w-10 h-10 bg-gradient-to-br from-primary to-teal-600 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                                AU
                            </div>
                            <span class="material-symbols-outlined text-slate-400 text-[20px] transition-transform group-hover:rotate-180">expand_more</span>
                        </div>
                        
                        <!-- Dropdown Menu - with padding-top to bridge the gap -->
                        <div class="absolute right-0 top-full pt-2 w-56 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700">
                                <div class="p-3 border-b border-slate-200 dark:border-slate-700">
                                    <div class="text-sm font-semibold text-slate-900 dark:text-white">Admin Usuario</div>
                                    <div class="text-xs text-slate-500">test@example.com</div>
                                </div>
                                <div class="p-2">
                                    <button 
                                        @click="logout"
                                        class="w-full flex items-center gap-3 px-3 py-2 text-left text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                    >
                                        <span class="material-symbols-outlined text-[20px]">logout</span>
                                        <span class="font-medium">Cerrar Sesión</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Dynamic Content Slot -->
            <div class="p-8 overflow-y-auto h-full bg-white dark:bg-slate-900">
                <router-view />
            </div>
        </main>
    </div>
</template>
