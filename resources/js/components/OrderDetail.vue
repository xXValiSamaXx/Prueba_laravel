<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const order = ref(null);
const isLoading = ref(true);

const fetchOrder = async () => {
    try {
        const response = await axios.get(`/api/orders/${route.params.id}`);
        order.value = response.data;
    } catch (error) {
        console.error('Error loading order:', error);
        alert('No se pudo cargar el pedido');
        router.push('/orders');
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchOrder();
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(value);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getStatusBadge = (status) => {
    const badges = {
        'paid': { class: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400', text: 'Pagado' },
        'pending': { class: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', text: 'Pendiente' },
        'cancelled': { class: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', text: 'Cancelado' }
    };
    return badges[status] || { class: 'bg-slate-100 text-slate-700', text: status };
};

const totalProducts = computed(() => {
    return order.value?.order_items?.reduce((sum, item) => sum + item.quantity, 0) || 0;
});

const goBack = () => {
    router.push('/orders');
};

const goToEdit = () => {
    router.push({ name: 'EditOrder', params: { id: route.params.id } });
};
</script>

<template>
    <div>
        <div v-if="isLoading" class="flex items-center justify-center h-96">
            <div class="text-slate-500">Cargando...</div>
        </div>

        <div v-else-if="order">
            <!-- Header -->
            <div class="mb-6">
                <button @click="goBack" class="flex items-center gap-2 text-slate-500 hover:text-primary transition-colors text-sm font-medium mb-4">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    Volver al Listado
                </button>
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Detalle del Pedido #{{ order.id }}</h1>
                        <p class="text-sm text-slate-500 mt-1">Información completa del pedido</p>
                    </div>
                    <button @click="goToEdit" class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-teal-700 transition-all font-semibold">
                        <span class="material-symbols-outlined text-[20px]">edit</span>
                        Editar Pedido
                    </button>
                </div>
            </div>

            <!-- Order Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Customer Card -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg">
                            <span class="material-symbols-outlined">person</span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-500 uppercase">Cliente</h3>
                    </div>
                    <p class="text-lg font-bold text-slate-900 dark:text-white">{{ order.customer.name }}</p>
                    <p class="text-sm text-slate-500 mt-1">{{ order.customer.email }}</p>
                    <p class="text-sm text-slate-500">{{ order.customer.phone }}</p>
                </div>

                <!-- Date & Time Card -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg">
                            <span class="material-symbols-outlined">schedule</span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-500 uppercase">Fecha y Hora</h3>
                    </div>
                    <p class="text-lg font-bold text-slate-900 dark:text-white">{{ formatDate(order.created_at) }}</p>
                    <p class="text-sm text-slate-500 mt-1">Fecha del pedido: {{ new Date(order.order_date).toLocaleDateString('es-ES') }}</p>
                </div>

                <!-- Status Card -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg">
                            <span class="material-symbols-outlined">info</span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-500 uppercase">Estado</h3>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" :class="getStatusBadge(order.status).class">
                        {{ getStatusBadge(order.status).text }}
                    </span>
                    <p class="text-sm text-slate-500 mt-3">Total: <span class="font-bold text-slate-900 dark:text-white">{{ formatCurrency(order.total_amount) }}</span></p>
                </div>
            </div>

            <!-- Products Table -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Productos del Pedido</h2>
                    <p class="text-sm text-slate-500 mt-1">Total de productos: {{ totalProducts }} unidades</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Producto</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Cantidad Comprada</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Stock Actual</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Precio Unitario</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr v-for="item in order.order_items" :key="item.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ item.product.name }}</p>
                                        <p class="text-xs text-slate-500">SKU: {{ item.product.sku }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-12 h-12 bg-primary/10 text-primary rounded-lg font-bold">
                                        {{ item.quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-medium" :class="item.product.stock > 10 ? 'text-emerald-600' : 'text-amber-600'">
                                        {{ item.product.stock }} disponibles
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ formatCurrency(item.unit_price) }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">{{ formatCurrency(item.subtotal) }}</span>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50 dark:bg-slate-800/50 border-t-2 border-slate-200 dark:border-slate-700">
                                <td colspan="4" class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300 uppercase">Total del Pedido:</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-xl font-bold text-primary">{{ formatCurrency(order.total_amount) }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
