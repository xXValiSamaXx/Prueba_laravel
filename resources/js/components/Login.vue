<script setup>
import { ref, reactive } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

const form = reactive({
    email: '',
    password: '',
    remember: false
});

const showPassword = ref(false);
const isLoading = ref(false);
const errorMessage = ref('');

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const handleLogin = async () => {
    isLoading.value = true;
    errorMessage.value = '';

    try {
        const response = await axios.post('/api/login', form);
        
        // Store token with the key that router expects
        if (response.data.token) {
            localStorage.setItem('auth_token', response.data.token);
            router.push('/dashboard');
        } else {
             // Fallback if token structure is different or successful login doesn't return token immediately (e.g. cookie-based)
             router.push('/dashboard');
        }


    } catch (error) {
        console.error('Login error:', error);
        if (error.response && error.response.data && error.response.data.message) {
            errorMessage.value = error.response.data.message;
        } else {
            errorMessage.value = 'Ha ocurrido un error al iniciar sesión. Por favor, inténtalo de nuevo.';
        }
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <div class="bg-soft-gray min-h-screen flex items-center justify-center p-4">
        <div class="max-w-5xl w-full bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row min-h-[600px]">
            <div class="w-full md:w-1/2 bg-navy p-12 flex flex-col justify-between relative overflow-hidden">
                <div class="absolute -top-24 -left-24 w-64 h-64 bg-primary/20 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>
                <div class="relative z-10 flex items-center gap-3">
                    <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center text-white shadow-lg">
                        <span class="material-symbols-outlined text-3xl">inventory_2</span>
                    </div>
                    <span class="font-bold text-2xl text-white tracking-tight">OrderManager</span>
                </div>
                <div class="relative z-10">
                    <h2 class="text-3xl font-bold text-white mb-4 leading-tight">Optimiza tu gestión de pedidos hoy mismo.
                    </h2>
                    <p class="text-slate-400 text-lg">Accede a tu panel de control para supervisar operaciones en tiempo
                        real y mejorar la eficiencia de tu equipo.</p>
                </div>
                <div class="relative z-10 text-slate-500 text-sm">
                    © 2024 OrderManager Platform. Todos los derechos reservados.
                </div>
            </div>
            <div class="w-full md:w-1/2 p-10 lg:p-16 flex flex-col justify-center">
                <div class="mb-10">
                    <h1 class="text-3xl font-bold text-navy mb-2">Bienvenido de nuevo</h1>
                    <p class="text-slate-500">Ingresa tus credenciales para acceder a tu cuenta.</p>
                </div>

                <!-- Error Alert -->
                <div v-if="errorMessage" class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                    <p class="text-sm font-bold">Error</p>
                    <p class="text-sm">{{ errorMessage }}</p>
                </div>

                <form @submit.prevent="handleLogin" class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-2" for="email">Correo Electrónico</label>
                        <div class="relative group">
                            <span
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 group-focus-within:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[20px]">mail</span>
                            </span>
                            <input
                                v-model="form.email"
                                class="block w-full pl-11 pr-4 py-3 bg-soft-gray border-none rounded-xl text-sm focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none"
                                id="email" 
                                name="email" 
                                placeholder="ejemplo@empresa.com" 
                                required 
                                type="email" 
                            />
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-semibold text-navy" for="password">Contraseña</label>
                        </div>
                        <div class="relative group">
                            <span
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 group-focus-within:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[20px]">lock</span>
                            </span>
                            <input
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                class="block w-full pl-11 pr-11 py-3 bg-soft-gray border-none rounded-xl text-sm focus:ring-2 focus:ring-primary focus:bg-white transition-all outline-none"
                                id="password" 
                                name="password" 
                                placeholder="••••••••" 
                                required
                            />
                            <button 
                                @click="togglePasswordVisibility"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
                                type="button"
                            >
                                <span class="material-symbols-outlined text-[20px]">{{ showPassword ? 'visibility_off' : 'visibility' }}</span>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input 
                                v-model="form.remember"
                                class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary cursor-pointer"
                                type="checkbox" 
                            />
                            <span class="text-sm text-slate-600 group-hover:text-navy transition-colors">Recordarme</span>
                        </label>
                        <a class="text-sm font-semibold text-primary hover:text-teal-700 transition-colors"
                            href="#">¿Olvidaste tu contraseña?</a>
                    </div>
                    <button
                        :disabled="isLoading"
                        class="w-full bg-primary hover:bg-teal-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-primary/20 transition-all transform active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed"
                        type="submit"
                    >
                        {{ isLoading ? 'Iniciando...' : 'Iniciar Sesión' }}
                    </button>
                </form>
                <p class="mt-10 text-center text-sm text-slate-500">
                    ¿Aún no tienes cuenta? <a class="font-semibold text-navy hover:text-primary transition-colors"
                        href="#">Solicitar acceso</a>
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Scoped styles if necessary, but Tailwind classes are preferred */
</style>
