<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';

const router = useRouter();
const orders = ref([]);

const fetchOrders = async () => {
    try {
        const response = await axios.get('/api/orders');
        orders.value = response.data;
    } catch (error) {
        console.error('Error fetching orders:', error);
    }
};

onMounted(() => {
    fetchOrders();
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(value);
};

const getStatusClass = (status) => {
    switch (status) {
        case 'paid':
            return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400';
        case 'pending':
            return 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
        case 'cancelled':
            return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
        default:
            return 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400';
    }
};

const getStatusLabel = (status) => {
    const labels = {
        'pending': 'Pendiente',
        'paid': 'Pagado',
        'cancelled': 'Cancelado'
    };
    return labels[status] || status;
};

const viewOrder = (id) => {
    router.push({ name: 'ViewOrder', params: { id } });
};

const editOrder = (id) => {
    router.push({ name: 'EditOrder', params: { id } });
};

const deleteOrder = (id) => {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción devolverá el stock.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await axios.delete('/api/orders/' + id);
                await fetchOrders();
                Swal.fire(
                    '¡Eliminado!',
                    'El pedido ha sido eliminado.',
                    'success'
                );
            } catch (error) {
                Swal.fire(
                    'Error',
                    'Hubo un problema al eliminar el pedido.',
                    'error'
                );
            }
        }
    });
};

const totalSales = computed(() => {
    return orders.value.reduce((sum, order) => sum + parseFloat(order.total_amount), 0);
});

const activeOrders = computed(() => {
    return orders.value.filter(order => order.status === 'pending').length;
});

const completedOrders = computed(() => {
    return orders.value.filter(order => order.status === 'paid').length;
});

const cancelledOrders = computed(() => {
    return orders.value.filter(order => order.status === 'cancelled').length;
});

// Navigation
const goToCreateOrder = () => {
    router.push({ name: 'CreateOrder' });
};

// Download Reports (PDF/Excel)
const downloadReport = async (type) => {
    const fileExtension = type === 'pdf' ? 'pdf' : 'xlsx';
    const fileName = `pedidos.${fileExtension}`;
    
    // Show loading alert
    Swal.fire({
        title: 'Generando reporte...',
        text: 'Por favor espera mientras se genera el archivo.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const response = await axios.get(`/api/orders/report/${type}`, {
            responseType: 'blob' // CRITICAL: Handle binary data
        });

        // Create temporary URL for the blob
        const url = window.URL.createObjectURL(new Blob([response.data]));
        
        // Create invisible <a> element
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', fileName);
        
        // Append to body, click, and cleanup
        document.body.appendChild(link);
        link.click();
        
        // Cleanup
        window.URL.revokeObjectURL(url);
        document.body.removeChild(link);

        // Success feedback
        Swal.fire({
            icon: 'success',
            title: '¡Descarga exitosa!',
            text: `El archivo ${fileName} se ha descargado correctamente.`,
            timer: 2000,
            showConfirmButton: false
        });

    } catch (error) {
        console.error('Error downloading report:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema al generar el reporte. Por favor, inténtalo de nuevo.'
        });
    }
};

</script>

<template>
    <div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Listado de Pedidos</h1>
                <p class="text-sm text-slate-500 mt-1">Gestiona y monitorea todos los pedidos entrantes.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button 
                    @click="downloadReport('pdf')"
                    class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium shadow-sm"
                >
                    <span class="material-symbols-outlined text-[20px]">picture_as_pdf</span>
                    Exportar PDF
                </button>
                <button 
                    @click="downloadReport('excel')"
                    class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-sm font-medium shadow-sm"
                >
                    <span class="material-symbols-outlined text-[20px] text-emerald-600">description</span>
                    Exportar Excel
                </button>
                <button 
                    @click="goToCreateOrder"
                    class="flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-lg hover:bg-teal-700 transition-all font-semibold shadow-md shadow-primary/20"
                >
                    <span class="material-symbols-outlined text-[20px]">add_circle</span>
                    Crear Nuevo Pedido
                </button>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Total</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Estado</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr v-for="order in orders" :key="order.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="text-sm font-semibold text-slate-900 dark:text-white">#{{ order.id }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 font-bold text-xs">
                                        {{ order.customer_name ? order.customer_name.substring(0, 2).toUpperCase() : 'N/A' }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ order.customer_name }}</p>
                                        <p class="text-[11px] text-slate-500">{{ order.customer_email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-600 dark:text-slate-400">{{ new Date(order.created_at).toLocaleDateString() }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-slate-900 dark:text-white">{{ formatCurrency(order.total_amount) }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusClass(order.status)">
                                    {{ getStatusLabel(order.status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="viewOrder(order.id)" class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/5 rounded-md transition-all" title="Ver Detalle">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </button>
                                    <button @click="editOrder(order.id)" class="p-1.5 text-slate-400 hover:text-amber-500 hover:bg-amber-50 rounded-md transition-all" title="Editar">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    <button @click="deleteOrder(order.id)" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-md transition-all" title="Eliminar">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-slate-500 font-medium">Mostrando <span class="text-slate-900 dark:text-white">{{ orders.length }}</span> pedidos</p>
                <!-- Pagination placeholders if needed generally go here -->
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
            <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 text-xs font-semibold uppercase">Total Ventas</span>
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg">
                        <span class="material-symbols-outlined text-[20px]">payments</span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ formatCurrency(totalSales) }}</h3>
                    <p class="text-xs text-emerald-600 mt-1 font-medium">↑ 12% vs mes anterior</p>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 text-xs font-semibold uppercase">Pedidos Activos</span>
                    <div class="p-2 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-lg">
                        <span class="material-symbols-outlined text-[20px]">pending_actions</span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ activeOrders }}</h3>
                    <p class="text-xs text-slate-400 mt-1">Esperando procesamiento</p>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 text-xs font-semibold uppercase">Completados</span>
                    <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg">
                        <span class="material-symbols-outlined text-[20px]">task_alt</span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ completedOrders }}</h3>
                    <p class="text-xs text-slate-400 mt-1">Este mes</p>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 text-xs font-semibold uppercase">Cancelaciones</span>
                    <div class="p-2 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg">
                        <span class="material-symbols-outlined text-[20px]">cancel</span>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ cancelledOrders }}</h3>
                    <p class="text-xs text-red-500 mt-1 font-medium">Bajo control</p>
                </div>
            </div>
        </div>
    </div>
</template>
