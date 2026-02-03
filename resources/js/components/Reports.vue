<template>
    <div class="p-8 overflow-y-auto flex-1 custom-scrollbar">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Reportes de Ventas y Analíticas</h1>
                <p class="text-slate-500 text-sm mt-1">Monitorea el rendimiento de ventas y tendencias en tiempo real.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg px-4 py-2 shadow-sm">
                    <span class="material-symbols-outlined text-slate-400 mr-2 text-sm">calendar_today</span>
                    <input class="bg-transparent border-none p-0 text-sm font-medium text-slate-700 dark:text-slate-300 focus:ring-0 cursor-pointer w-48" readonly type="text" value="01 Oct, 2025 - 31 Oct, 2025"/>
                    <span class="material-symbols-outlined text-slate-400 ml-1 text-sm">expand_more</span>
                </div>
                <div class="relative group">
                    <button @click="showExportMenu = !showExportMenu" class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-teal-700 transition-all text-sm font-semibold shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">download</span>
                        Exportar Reporte
                    </button>
                    <div v-if="showExportMenu" class="absolute right-0 mt-2 w-40 bg-white dark:bg-slate-800 rounded-lg shadow-xl border border-slate-200 dark:border-slate-700 z-10">
                        <button @click="showExportAlert" class="w-full text-left px-4 py-2 text-xs font-medium hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-red-500">picture_as_pdf</span> Documento PDF
                        </button>
                        <button @click="showExportAlert" class="w-full text-left px-4 py-2 text-xs font-medium hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-green-600">table_view</span> Hoja de Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-teal-50 dark:bg-teal-900/20 rounded-lg">
                        <span class="material-symbols-outlined text-primary">payments</span>
                    </div>
                    <span class="text-xs font-bold text-green-500 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded">+12.5%</span>
                </div>
                <p class="text-slate-500 text-sm font-medium">Ingresos Totales</p>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">$128,430.00</h3>
            </div>
            <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <span class="material-symbols-outlined text-blue-600">shopping_bag</span>
                    </div>
                    <span class="text-xs font-bold text-green-500 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded">+8.2%</span>
                </div>
                <p class="text-slate-500 text-sm font-medium">Volumen de Pedidos</p>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">1,452</h3>
            </div>
            <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                        <span class="material-symbols-outlined text-amber-600">request_quote</span>
                    </div>
                    <span class="text-xs font-bold text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded">-2.4%</span>
                </div>
                <p class="text-slate-500 text-sm font-medium">Valor Promedio del Pedido</p>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">$88.45</h3>
            </div>
            <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <span class="material-symbols-outlined text-purple-600">group</span>
                    </div>
                    <span class="text-xs font-bold text-green-500 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded">+15.1%</span>
                </div>
                <p class="text-slate-500 text-sm font-medium">Nuevos Clientes</p>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">328</h3>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 bg-white dark:bg-slate-900 p-8 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Ingresos en el Tiempo</h2>
                        <p class="text-xs text-slate-500">Rendimiento de ventas en el periodo seleccionado</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="flex items-center gap-1.5 text-xs font-medium text-slate-500">
                            <span class="w-2.5 h-2.5 rounded-full bg-primary"></span> Actual
                        </span>
                        <span class="flex items-center gap-1.5 text-xs font-medium text-slate-500">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-200 dark:bg-slate-700"></span> Anterior
                        </span>
                    </div>
                </div>
                <div class="h-72 w-full relative chart-placeholder flex items-end justify-between px-4">
                    <div class="absolute inset-0 flex items-end">
                        <svg class="w-full h-full opacity-20" preserveAspectRatio="none" viewBox="0 0 400 100">
                            <path d="M0,80 Q50,60 100,70 T200,40 T300,50 T400,20 V100 H0 Z" fill="var(--primary-color)"></path>
                        </svg>
                        <svg class="absolute inset-0 w-full h-full" preserveAspectRatio="none" viewBox="0 0 400 100">
                            <path class="text-primary" d="M0,80 Q50,60 100,70 T200,40 T300,50 T400,20" fill="none" stroke="currentColor" stroke-width="2"></path>
                        </svg>
                    </div>
                    <div class="w-full flex justify-between text-[10px] font-medium text-slate-400 mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <span>Oct 01</span>
                        <span>Oct 07</span>
                        <span>Oct 14</span>
                        <span>Oct 21</span>
                        <span>Oct 28</span>
                        <span>Oct 31</span>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-900 p-8 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="mb-8">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Ventas por Categoría</h2>
                    <p class="text-xs text-slate-500">Distribución de ingresos por tipo de producto</p>
                </div>
                <div class="relative flex justify-center mb-8">
                    <div class="w-48 h-48 rounded-full border-[20px] border-slate-100 dark:border-slate-800 relative">
                        <div class="absolute inset-0 rounded-full border-[20px] border-primary border-r-transparent border-b-transparent rotate-45"></div>
                        <div class="absolute inset-0 rounded-full border-[20px] border-amber-500 border-l-transparent border-t-transparent -rotate-12"></div>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl font-bold text-slate-900 dark:text-white">$128k</span>
                            <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">Total</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-primary"></span>
                            <span class="text-sm text-slate-600 dark:text-slate-400">Licencias de Software</span>
                        </div>
                        <span class="text-sm font-bold">58%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                            <span class="text-sm text-slate-600 dark:text-slate-400">Hosting en la Nube</span>
                        </div>
                        <span class="text-sm font-bold">24%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-slate-400"></span>
                            <span class="text-sm text-slate-600 dark:text-slate-400">Servicios de Consultoría</span>
                        </div>
                        <span class="text-sm font-bold">18%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Transacciones Recientes</h2>
                <button class="text-primary text-sm font-bold hover:underline">Ver Todo</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 uppercase text-xs font-bold">
                        <tr>
                            <th class="px-6 py-4">ID Orden</th>
                            <th class="px-6 py-4">Cliente</th>
                            <th class="px-6 py-4 text-center">Fecha</th>
                            <th class="px-6 py-4 text-center">Artículos</th>
                            <th class="px-6 py-4 text-right">Monto</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 font-semibold text-primary">#ORD-2025-001</td>
                            <td class="px-6 py-4">Acme Corp</td>
                            <td class="px-6 py-4 text-center text-slate-500">Oct 28, 2025</td>
                            <td class="px-6 py-4 text-center">4</td>
                            <td class="px-6 py-4 text-right font-bold">$2,450.00</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-full text-[10px] font-bold">COMPLETADO</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 font-semibold text-primary">#ORD-2025-002</td>
                            <td class="px-6 py-4">Beta Solutions</td>
                            <td class="px-6 py-4 text-center text-slate-500">Oct 27, 2025</td>
                            <td class="px-6 py-4 text-center">12</td>
                            <td class="px-6 py-4 text-right font-bold">$12,800.00</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 rounded-full text-[10px] font-bold">PROCESANDO</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 font-semibold text-primary">#ORD-2025-003</td>
                            <td class="px-6 py-4">Global Industries</td>
                            <td class="px-6 py-4 text-center text-slate-500">Oct 25, 2025</td>
                            <td class="px-6 py-4 text-center">2</td>
                            <td class="px-6 py-4 text-right font-bold">$850.00</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-full text-[10px] font-bold">COMPLETADO</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import Swal from 'sweetalert2';

const showExportMenu = ref(false);

const showExportAlert = () => {
    showExportMenu.value = false;
    Swal.fire({
        icon: 'info',
        title: 'Próximamente',
        text: 'Se esta trabajando en ello',
        timer: 2000,
        showConfirmButton: false
    });
};
</script>

<style scoped>
.chart-placeholder {
    background-image: radial-gradient(circle at 2px 2px, rgba(0,0,0,0.05) 1px, transparent 0);
    background-size: 24px 24px;
}
.dark .chart-placeholder {
    background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.05) 1px, transparent 0);
}
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
