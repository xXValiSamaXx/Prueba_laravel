import { createRouter, createWebHistory } from 'vue-router';

// Importa tus componentes (Asegúrate de que las rutas sean correctas)
import Login from './components/Login.vue';
import MainLayout from './components/MainLayout.vue';
import DashboardHome from './components/DashboardHome.vue';
import OrderList from './components/OrderList.vue';
import OrderForm from './components/OrderForm.vue';
import OrderDetail from './components/OrderDetail.vue';
import CustomerList from './components/CustomerList.vue';
import ProductList from './components/ProductList.vue';

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: Login,
        meta: { requiresAuth: false } // Login no necesita auth
    },
    {
        path: '/',
        component: MainLayout, // El Layout envuelve a las demás vistas
        meta: { requiresAuth: true }, // Estas rutas requieren login
        children: [
            {
                path: '',
                redirect: '/dashboard' // Redirige raíz a dashboard
            },
            {
                path: 'dashboard',
                name: 'Dashboard',
                component: DashboardHome
            },
            {
                path: 'orders',
                name: 'Orders',
                component: OrderList
            },
            {
                path: 'orders/create',
                name: 'CreateOrder',
                component: OrderForm
            },
            {
                path: 'orders/:id',
                name: 'ViewOrder',
                component: OrderDetail // Read-only view
            },
            {
                path: 'orders/:id/edit',
                name: 'EditOrder',
                component: OrderForm
            },
            {
                path: 'customers',
                name: 'Customers',
                component: CustomerList
            },
            {
                path: 'products',
                name: 'Products',
                component: ProductList
            }
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// Guardia de Navegación (Simple)
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('auth_token'); // O como guardes tu token

    if (to.meta.requiresAuth && !token) {
        next('/login'); // Si no hay token y la ruta es protegida -> Login
    } else if (to.path === '/login' && token) {
        next('/dashboard'); // Si ya hay token y vas al login -> Dashboard
    } else {
        next();
    }
});

export default router;