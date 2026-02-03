<template>
    <div class="p-8 overflow-y-auto flex-1 custom-scrollbar">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Listado de Clientes</h1>
                    <p class="text-sm text-slate-500 mt-1">Administra y gestiona la base de datos de tus clientes registrados.</p>
                </div>
                <button class="flex items-center justify-center gap-2 px-5 py-2.5 bg-primary text-white rounded-lg hover:bg-teal-700 transition-all font-semibold shadow-md shadow-primary/20">
                    <span class="material-symbols-outlined text-[20px]">person_add</span>
                    Agregar Nuevo Cliente
                </button>
            </div>

            <!-- Search Bar -->
            <div class="mb-6">
                <div class="relative group max-w-lg">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 group-focus-within:text-primary transition-colors">
                        <span class="material-symbols-outlined">search</span>
                    </span>
                    <input 
                        v-model="searchQuery"
                        @input="handleSearch"
                        class="block w-full pl-10 pr-3 py-2 border-none bg-slate-100 dark:bg-slate-800 rounded-full text-sm focus:ring-2 focus:ring-primary transition-all" 
                        placeholder="Buscar clientes, correos o ID..." 
                        type="text"
                    />
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <!-- Loading State -->
                <div v-if="loading" class="p-8 text-center">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                    <p class="mt-2 text-sm text-slate-500">Cargando clientes...</p>
                </div>

                <!-- Table -->
                <div v-else class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Perfil</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Cliente</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Total Pedidos</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Total Gastado</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Último Pedido</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr v-if="customers.length === 0">
                                <td colspan="7" class="px-6 py-8 text-center text-slate-500">
                                    No se encontraron clientes
                                </td>
                            </tr>
                            <tr 
                                v-for="customer in customers" 
                                :key="customer.id"
                                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors"
                            >
                                <!-- Profile Picture -->
                                <td class="px-6 py-4">
                                    <img 
                                        v-if="customer.photo" 
                                        :alt="customer.name" 
                                        :src="customer.photo"
                                        class="w-10 h-10 rounded-full object-cover"
                                    />
                                    <div 
                                        v-else
                                        class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 font-bold text-sm"
                                    >
                                        {{ getInitials(customer.name) }}
                                    </div>
                                </td>

                                <!-- Customer Name -->
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ customer.name }}</span>
                                </td>

                                <!-- Email -->
                                <td class="px-6 py-4">
                                    <span class="text-sm text-slate-600 dark:text-slate-400">{{ customer.email }}</span>
                                </td>

                                <!-- Total Orders Badge -->
                                <td class="px-6 py-4 text-center">
                                    <span 
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            (customer.orders_count || 0) >= 10 
                                                ? 'bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-400'
                                                : 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-400'
                                        ]"
                                    >
                                        {{ customer.orders_count || 0 }} Pedidos
                                    </span>
                                </td>

                                <!-- Total Spent -->
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">
                                        {{ formatCurrency(customer.total_spent || 0) }}
                                    </span>
                                </td>

                                <!-- Last Order Date -->
                                <td class="px-6 py-4">
                                    <div v-if="customer.last_order_date" class="flex flex-col">
                                        <span class="text-sm text-slate-700 dark:text-slate-300">
                                            {{ formatDate(customer.last_order_date) }}
                                        </span>
                                        <span class="text-[11px] text-slate-400">
                                            {{ getRelativeTime(customer.last_order_date) }}
                                        </span>
                                    </div>
                                    <span v-else class="text-sm text-slate-400 italic">Sin pedidos</span>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button 
                                            @click="viewCustomer(customer.id)"
                                            class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-all" 
                                            title="Ver Detalles"
                                        >
                                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                                        </button>
                                        <button 
                                            @click="editCustomer(customer.id)"
                                            class="p-2 text-slate-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-all" 
                                            title="Editar"
                                        >
                                            <span class="material-symbols-outlined text-[20px]">edit</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-slate-500">
                        Mostrando 
                        <span class="font-semibold text-slate-900 dark:text-white">{{ pagination.from || 0 }}</span> 
                        a 
                        <span class="font-semibold text-slate-900 dark:text-white">{{ pagination.to || 0 }}</span> 
                        de 
                        <span class="font-semibold text-slate-900 dark:text-white">{{ pagination.total || 0 }}</span> 
                        clientes
                    </div>
                    <div class="flex items-center gap-1">
                        <button 
                            @click="goToPage(pagination.current_page - 1)"
                            :disabled="!pagination.prev_page_url"
                            class="p-2 text-slate-400 hover:text-primary hover:bg-white dark:hover:bg-slate-800 rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                        </button>
                        
                        <template v-for="page in visiblePages" :key="page">
                            <button 
                                v-if="page !== '...'"
                                @click="goToPage(page)"
                                :class="[
                                    'w-8 h-8 flex items-center justify-center text-sm font-semibold rounded-lg transition-all',
                                    page === pagination.current_page
                                        ? 'bg-primary text-white'
                                        : 'text-slate-600 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-800'
                                ]"
                            >
                                {{ page }}
                            </button>
                            <span v-else class="px-2 text-slate-400">...</span>
                        </template>

                        <button 
                            @click="goToPage(pagination.current_page + 1)"
                            :disabled="!pagination.next_page_url"
                            class="p-2 text-slate-400 hover:text-primary hover:bg-white dark:hover:bg-slate-800 rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                        </button>
                    </div>
                </div>
            </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

// State
const customers = ref([]);
const pagination = ref({
    current_page: 1,
    last_page: 1,
    from: 0,
    to: 0,
    total: 0,
    prev_page_url: null,
    next_page_url: null
});
const loading = ref(false);
const searchQuery = ref('');
let searchTimeout = null;

// Helper Functions
const getInitials = (name) => {
    if (!name) return '??';
    const parts = name.trim().split(' ');
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN'
    }).format(amount);
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
};

const getRelativeTime = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) return 'Hoy';
    if (diffDays === 1) return 'Ayer';
    if (diffDays < 7) return `Hace ${diffDays} días`;
    if (diffDays < 30) return `Hace ${Math.floor(diffDays / 7)} semanas`;
    return `Hace ${Math.floor(diffDays / 30)} meses`;
};

// Computed
const visiblePages = computed(() => {
    const current = pagination.value.current_page;
    const last = pagination.value.last_page;
    const pages = [];
    
    if (last <= 7) {
        // Show all pages if 7 or fewer
        for (let i = 1; i <= last; i++) {
            pages.push(i);
        }
    } else {
        // Always show first page
        pages.push(1);
        
        if (current > 3) {
            pages.push('...');
        }
        
        // Show pages around current
        for (let i = Math.max(2, current - 1); i <= Math.min(last - 1, current + 1); i++) {
            pages.push(i);
        }
        
        if (current < last - 2) {
            pages.push('...');
        }
        
        // Always show last page
        pages.push(last);
    }
    
    return pages;
});

// API Functions
const fetchCustomers = async (page = 1, search = '') => {
    loading.value = true;
    try {
        const params = { page };
        if (search) {
            params.search = search;
        }
        
        const response = await axios.get('/api/customers', { params });
        
        customers.value = response.data.data;
        pagination.value = {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            from: response.data.from,
            to: response.data.to,
            total: response.data.total,
            prev_page_url: response.data.prev_page_url,
            next_page_url: response.data.next_page_url
        };
    } catch (error) {
        console.error('Error fetching customers:', error);
        customers.value = [];
    } finally {
        loading.value = false;
    }
};

const goToPage = (page) => {
    if (page >= 1 && page <= pagination.value.last_page) {
        fetchCustomers(page, searchQuery.value);
    }
};

const handleSearch = () => {
    // Debounce search
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchCustomers(1, searchQuery.value);
    }, 500);
};

const viewCustomer = (id) => {
    router.push(`/customers/${id}`);
};

const editCustomer = (id) => {
    router.push(`/customers/${id}/edit`);
};

// Lifecycle
onMounted(() => {
    fetchCustomers();
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: #334155;
}
</style>
