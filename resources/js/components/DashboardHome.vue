<script setup>
import { reactive } from 'vue';

const stats = reactive({
    totalSales: '$128,430.00',
    salesTrend: '12%',
    pendingOrders: 42,
    urgentOrders: 8,
    newCustomers: 156,
    customerGrowth: '5.4%'
});

const salesPerformance = [
    { month: 'ENE', height: 'h-32', color: 'bg-primary/40' },
    { month: 'FEB', height: 'h-40', color: 'bg-primary/60' },
    { month: 'MAR', height: 'h-48', color: 'bg-primary/80' },
    { month: 'ABR', height: 'h-56', color: 'bg-primary' },
    { month: 'MAY', height: 'h-44', color: 'bg-primary/70' },
    { month: 'JUN', height: 'h-52', color: 'bg-primary/90' },
];

const topProducts = [
    {
        name: 'Licencia Cloud Pro',
        sales: 420,
        revenue: '$12,400',
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuClV327rSAjLY6AqaSWjFJAFzbC3L3qx2aAggyMnjH4ADg6sUp1pfaEfb0lXcn2Dr__sZ4_V9yo5liRbzYtWXFbVlFIqameA5KVEMcYtGmG8vurAQDJzbxkm6Tbi8LoKeFSQVgE9NogcPRLpLyfSQgq1_-bU12k6BSGPr22Kb3roS8OUqg5alyzBOrqevSGw5Yn1_o7trx7ss1P13dqgtlASps6VrDJ_hJVjP6XiGVHZsV4_VKdpjcUIcY4hKDQX41JjxNTSsd0UdYt'
    },
    {
        name: 'Pack Soporte Premium',
        sales: 285,
        revenue: '$8,920',
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCheefqlCF-T5-fss5J-79juhnWlnGiJ0_MFPnUO8Uo5hNBEoLka2WEnoMcIzNjnQLa1QQAM-dl4fQhCsMxgtjftIs9y5bBQAbCvwV93FSD3R7LzYUxU4Bw0_Se2Ufr5YR4RSz5U85Uo7kf2Bz7IfDgvYct0y776IQaKQltV7cOwyf1w3_FQIq4qHdGxG5_2C2CV0fZalsdWU05QwLPObT7-jLtYiWpM4xaC3ISitJPcMUnsuYUO_UsUTpQ4i8YzYeFEMQ6dN3UHXWy'
    },
    {
        name: 'API Integration Kit',
        sales: 190,
        revenue: '$5,600',
        image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuAwms8Jm0pBc7hJpGhRY-9xi6sNJWXVW4acQgiHF1FiiRZpzL6EWouEMqb9kwZQl-ZVnwOMeo254kOF6SjsLRctQHWlpwWBltBIxx0gZ5nbVWnUt-MgNQ1rLBtJ33pW19FCuPxr0ayETqE7QwmKAbg0wW9vW1ZyHvCFDvFsEiBQMwWeo4rXWF68aCNixj9C6mljM2jPaNnAXn-4O7z2hsYvSq-vPX6cyf23UUARrGWphHYDO7_JkKjd4UTq40LGAZsmriBQ-58o2X5k'
    }
];

const recentOrders = [
    {
        id: '#ORD-2025-01',
        customer: 'Acme Corporation',
        customerInitials: 'AC',
        date: '01 Nov, 2025',
        total: '$2,400.00',
        status: 'Pagado'
    },
    {
        id: '#ORD-2025-02',
        customer: 'Beta Solutions',
        customerInitials: 'BS',
        date: '01 Nov, 2025',
        total: '$1,150.00',
        status: 'Pendiente'
    },
    {
        id: '#ORD-2025-03',
        customer: 'Gamma S.A.',
        customerInitials: 'GS',
        date: '31 Oct, 2025',
        total: '$450.00',
        status: 'Pagado'
    }
];

const getStatusColor = (status) => {
    if (status === 'Pagado') {
        return {
            badge: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
            dot: 'bg-emerald-500'
        };
    } else if (status === 'Pendiente') {
        return {
            badge: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
            dot: 'bg-amber-500'
        };
    }
    return {
        badge: 'bg-slate-100 text-slate-700',
        dot: 'bg-slate-500'
    };
};
</script>

<template>
    <div>
        <!-- Welcome Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Resumen General</h1>
                <p class="text-slate-500 text-sm mt-1">Monitorización del sistema en tiempo real.</p>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div
                class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-slate-500 text-sm font-medium">Ventas Totales</p>
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ stats.totalSales }}</h3>
                    </div>
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 rounded-lg">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span
                        class="flex items-center text-emerald-600 font-semibold bg-emerald-50 dark:bg-emerald-900/20 px-1.5 py-0.5 rounded">
                        <span class="material-symbols-outlined text-xs">trending_up</span>
                        {{ stats.salesTrend }}
                    </span>
                    <span class="text-slate-400">vs. mes anterior</span>
                </div>
            </div>
            <div
                class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-slate-500 text-sm font-medium">Pedidos Pendientes</p>
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ stats.pendingOrders }}
                        </h3>
                    </div>
                    <div class="p-2 bg-amber-50 dark:bg-amber-900/20 text-amber-600 rounded-lg">
                        <span class="material-symbols-outlined">schedule</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="flex items-center text-amber-600 font-semibold">
                        {{ stats.urgentOrders }} necesitan atención hoy
                    </span>
                </div>
            </div>
            <div
                class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-slate-500 text-sm font-medium">Nuevos Clientes</p>
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ stats.newCustomers }}</h3>
                    </div>
                    <div class="p-2 bg-sky-50 dark:bg-sky-900/20 text-sky-600 rounded-lg">
                        <span class="material-symbols-outlined">person_add</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span
                        class="flex items-center text-sky-600 font-semibold bg-sky-50 dark:bg-sky-900/20 px-1.5 py-0.5 rounded">
                        <span class="material-symbols-outlined text-xs">trending_up</span>
                        {{ stats.customerGrowth }}
                    </span>
                    <span class="text-slate-400">crecimiento este mes</span>
                </div>
            </div>
        </div>

        <!-- Charts and Top Products -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div
                class="lg:col-span-2 bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Rendimiento de Ventas</h2>
                        <p class="text-sm text-slate-500">Volumen mensual de pedidos</p>
                    </div>
                    <select
                        class="text-sm border-slate-200 dark:border-slate-700 bg-transparent rounded-lg focus:ring-primary">
                        <option>Últimos 6 meses</option>
                        <option>Último año</option>
                    </select>
                </div>
                <div class="h-64 flex items-end justify-between gap-4 px-2">
                    <div v-for="item in salesPerformance" :key="item.month" class="flex flex-col items-center flex-1">
                        <div :class="[item.height]" class="w-full bg-slate-100 dark:bg-slate-800 rounded-t-md relative">
                            <div :class="[item.color]" class="absolute bottom-0 w-full rounded-t-md h-[90%]"></div>
                        </div>
                        <span class="text-[10px] text-slate-400 mt-2 font-medium">{{ item.month }}</span>
                    </div>
                </div>
            </div>
            <div
                class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Productos Más Vendidos</h2>
                <div class="space-y-6">
                    <div v-for="product in topProducts" :key="product.name" class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center justify-center overflow-hidden">
                            <img :src="product.image" alt="Product" class="object-cover w-full h-full" />
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ product.name }}</h4>
                            <p class="text-xs text-slate-500">{{ product.sales }} unidades vendidas</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-primary">{{ product.revenue }}</span>
                        </div>
                    </div>
                </div>
                <button
                    class="w-full mt-8 py-2 text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-primary transition-colors border border-dashed border-slate-300 dark:border-slate-700 rounded-lg">
                    Ver todo el inventario
                </button>
            </div>
        </div>

        <!-- Recent Orders Table -->
        <div
            class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Pedidos Recientes</h2>
                <a class="text-sm font-semibold text-primary hover:underline" href="#">Ver todos</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr
                            class="text-[10px] uppercase tracking-wider text-slate-500 bg-slate-50 dark:bg-slate-800/50">
                            <th class="px-6 py-4 font-bold">Pedido</th>
                            <th class="px-6 py-4 font-bold">Cliente</th>
                            <th class="px-6 py-4 font-bold">Fecha</th>
                            <th class="px-6 py-4 font-bold">Total</th>
                            <th class="px-6 py-4 font-bold">Estado</th>
                            <th class="px-6 py-4 font-bold text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr v-for="order in recentOrders" :key="order.id"
                            class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-slate-900 dark:text-white">{{ order.id }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-7 h-7 rounded-full bg-primary/10 text-primary flex items-center justify-center text-[10px] font-bold">
                                        {{ order.customerInitials }}</div>
                                    <span class="text-sm text-slate-600 dark:text-slate-300">{{ order.customer }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ order.date }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white">{{ order.total }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="getStatusColor(order.status).badge">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="getStatusColor(order.status).dot"></span>
                                    {{ order.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="p-1 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
