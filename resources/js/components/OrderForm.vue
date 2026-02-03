<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';

const route = useRoute();
const router = useRouter();

const customers = ref([]);
const products = ref([]);
const isEditing = ref(false);
const orderId = ref(null);
const isLoading = ref(false);

const form = reactive({
    customer_id: '',
    order_date: new Date().toISOString().split('T')[0],
    status: 'pending',
    items: []
});

// Fetch initial data
const fetchData = async () => {
    try {
        const [customersRes, productsRes] = await Promise.all([
            axios.get('/api/customers'),
            axios.get('/api/products')
        ]);
        customers.value = customersRes.data;
        products.value = productsRes.data;

        // Check if editing
        if (route.params.id) {
            isEditing.value = true;
            orderId.value = route.params.id;
            await loadOrder(orderId.value);
        } else {
            // Add one empty item by default for new orders
            addItem();
        }
    } catch (error) {
        console.error('Error loading data:', error);
        Swal.fire('Error', 'No se pudieron cargar los datos necesarios.', 'error');
    }
};

const loadOrder = async (id) => {
    try {
        const response = await axios.get(`/api/orders/${id}`);
        const order = response.data;
        
        form.customer_id = order.customer_id;
        // Extract only the date part (YYYY-MM-DD) from ISO datetime
        form.order_date = order.order_date.split('T')[0];
        form.status = order.status;
        
        // Map items from order_items (note the underscore)
        if (order.order_items && order.order_items.length > 0) {
            form.items = order.order_items.map(item => ({
                product_id: item.product_id,
                product_name: item.product.name, // Store name for display
                quantity: item.quantity,
                unit_price: parseFloat(item.unit_price),
                stock: item.product.stock // Store current stock
            }));
        } else {
             addItem();
        }

    } catch (error) {
        console.error('Error loading order:', error);
        Swal.fire('Error', 'No se pudo cargar el pedido.', 'error');
        router.push('/orders');
    }
};

onMounted(() => {
    fetchData();
});

// Logic
const addItem = () => {
    form.items.push({
        product_id: '',
        quantity: 1,
        unit_price: 0
    });
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const onProductChange = (item) => {
    const product = products.value.find(p => p.id === item.product_id);
    if (product) {
        item.unit_price = product.price;
        // Validate quantity instantly against new product stock
        validateStock(item);
    } else {
        item.unit_price = 0;
    }
};

const getProductStock = (productId) => {
    const product = products.value.find(p => p.id === productId);
    return product ? product.stock : 0;
};

const validateStock = (item) => {
    // Only used for immediate UI feedback if needed, mainly controlled by template classes
};

const isStockInsufficient = (item) => {
    // In edit mode, use the stock stored in the item
    // In create mode, get stock from products list
    const stock = isEditing.value ? (item.stock || 0) : getProductStock(item.product_id);
    return item.product_id && item.quantity > stock;
};

const calculateSubtotal = (item) => {
    return item.quantity * item.unit_price;
};

const totalOrder = computed(() => {
    return form.items.reduce((sum, item) => sum + calculateSubtotal(item), 0);
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(value);
};

const submitOrder = async () => {
    if (form.items.length === 0) {
        Swal.fire('Atención', 'Debes agregar al menos un producto.', 'warning');
        return;
    }

    // Validate fields
    if (!form.customer_id) {
         Swal.fire('Atención', 'Selecciona un cliente.', 'warning');
         return;
    }

    // Validate stock
    const insufficientStockItem = form.items.find(item => isStockInsufficient(item));
    if (insufficientStockItem) {
        Swal.fire('Error de Stock', 'Uno o más productos superan el stock disponible.', 'error');
        return;
    }

    // Validate empty products
    if (form.items.some(item => !item.product_id || item.quantity <= 0)) {
         Swal.fire('Atención', 'Verifica que todos los items tengan producto y cantidad válida.', 'warning');
         return;
    }

    isLoading.value = true;

    try {
        if (isEditing.value) {
            await axios.put(`/api/orders/${orderId.value}`, form);
            await Swal.fire('¡Actualizado!', 'El pedido ha sido actualizado correctamente.', 'success');
        } else {
            await axios.post('/api/orders', form);
            await Swal.fire('¡Creado!', 'El pedido ha sido creado correctamente.', 'success');
        }
        router.push('/orders');
    } catch (error) {
        console.error('Error saving order:', error);
        Swal.fire('Error', 'Hubo un problema al guardar el pedido.', 'error');
    } finally {
        isLoading.value = false;
    }
};

const cancel = () => {
    router.push('/orders');
};

</script>

<template>
    <div>
        <div class="p-8 overflow-y-auto">
            <div class="mb-6">
                <button @click="cancel" class="flex items-center gap-2 text-slate-500 hover:text-primary transition-colors text-sm font-medium mb-4">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    Volver al Listado
                </button>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ isEditing ? `Editar Pedido #${orderId}` : 'Crear Nuevo Pedido' }}</h1>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <form @submit.prevent="submitOrder" class="p-8">
                    <div class="mb-10">
                        <div class="flex items-center gap-2 mb-6 border-b border-slate-100 dark:border-slate-800 pb-2">
                            <span class="material-symbols-outlined text-primary">info</span>
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-white">Información del Pedido</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Cliente</label>
                                <div class="relative">
                                    <select v-model="form.customer_id" :disabled="isEditing" class="block w-full appearance-none bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none disabled:opacity-60 disabled:cursor-not-allowed">
                                        <option disabled value="">Seleccione un Cliente...</option>
                                        <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                            {{ customer.name || customer.label || 'Cliente ' + customer.id }}
                                        </option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                                        <span class="material-symbols-outlined">expand_more</span>
                                    </div>
                                </div>
                                <p v-if="isEditing" class="text-xs text-amber-600 dark:text-amber-400">⚠️ No se puede cambiar el cliente</p>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Fecha del Pedido</label>
                                <input v-model="form.order_date" :disabled="isEditing" class="block w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none disabled:opacity-60 disabled:cursor-not-allowed" type="date" />
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Estado del Pedido</label>
                                <div class="relative">
                                    <select v-model="form.status" class="block w-full appearance-none bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none">
                                        <option value="pending">Pendiente</option>
                                        <option value="paid">Pagado</option>
                                        <option value="cancelled">Cancelado</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                                        <span class="material-symbols-outlined">expand_more</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-10">
                        <div class="flex items-center gap-2 mb-6 border-b border-slate-100 dark:border-slate-800 pb-2">
                            <span class="material-symbols-outlined text-primary">shopping_cart</span>
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-white">Productos del Pedido</h2>
                        </div>
                        <div class="space-y-4">
                            <div class="hidden md:grid grid-cols-12 gap-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                                <div class="col-span-6">Producto</div>
                                <div class="col-span-2 text-center">Cantidad</div>
                                <div class="col-span-2 text-right">Precio Unitario</div>
                                <div class="col-span-2 text-right">Subtotal</div>
                            </div>
                            
                            <div v-for="(item, index) in form.items" :key="index" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center bg-slate-50/50 dark:bg-slate-800/30 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                                <div class="col-span-6">
                                    <label class="block md:hidden text-xs font-bold text-slate-500 uppercase mb-1">Producto</label>
                                    
                                    <!-- Edit Mode: Show product name as text -->
                                    <div v-if="isEditing">
                                        <div class="px-4 py-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg">
                                            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ item.product_name }}</p>
                                            <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">⚠️ No se puede cambiar el producto</p>
                                        </div>
                                        <div class="mt-1 text-xs text-slate-500">
                                            Stock disponible: <span class="font-bold">{{ item.stock }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Create Mode: Show product dropdown -->
                                    <div v-else>
                                        <div class="relative">
                                            <select 
                                                v-model="item.product_id" 
                                                @change="onProductChange(item)"
                                                class="block w-full appearance-none bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary transition-all outline-none"
                                            >
                                                <option value="">Seleccione Producto</option>
                                                <option v-for="product in products" :key="product.id" :value="product.id">
                                                    {{ product.name }}
                                                </option>
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                                                <span class="material-symbols-outlined text-[20px]">expand_more</span>
                                            </div>
                                        </div>
                                        <div v-if="item.product_id" class="mt-1 text-xs text-slate-500">
                                            Stock disponible: <span class="font-bold">{{ getProductStock(item.product_id) }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-span-2">
                                    <label class="block md:hidden text-xs font-bold text-slate-500 uppercase mb-1">Cantidad</label>
                                    <input 
                                        v-model.number="item.quantity" 
                                        class="block w-full text-center bg-white dark:bg-slate-900 border rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary outline-none"
                                        :class="isStockInsufficient(item) ? 'border-red-500 text-red-600 focus:border-red-500 focus:ring-red-200' : 'border-slate-200 dark:border-slate-700'"
                                        min="1" 
                                        type="number"
                                    />
                                    <div v-if="isStockInsufficient(item)" class="text-[10px] text-red-500 mt-1 font-bold text-center">
                                        Excede Stock
                                    </div>
                                </div>
                                
                                <div class="col-span-2">
                                    <label class="block md:hidden text-xs font-bold text-slate-500 uppercase mb-1">Precio Unitario</label>
                                    <div class="text-sm font-medium text-slate-600 dark:text-slate-400 text-right md:pr-2">
                                        {{ formatCurrency(item.unit_price) }}
                                    </div>
                                </div>
                                
                                <div class="col-span-2 flex items-center justify-between gap-4">
                                    <div class="flex-1 text-right">
                                        <label class="block md:hidden text-xs font-bold text-slate-500 uppercase mb-1 text-right">Subtotal</label>
                                        <span class="text-sm font-bold text-slate-900 dark:text-white">
                                            {{ formatCurrency(calculateSubtotal(item)) }}
                                        </span>
                                    </div>
                                    <!-- Only allow removing items in create mode -->
                                    <button v-if="!isEditing" @click="removeItem(index)" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" type="button">
                                        <span class="material-symbols-outlined text-[20px]">cancel</span>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Only allow adding items in create mode -->
                            <button v-if="!isEditing" @click="addItem" class="mt-4 flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 text-primary border border-primary/20 hover:border-primary hover:bg-primary/5 rounded-lg transition-all text-sm font-semibold shadow-sm" type="button">
                                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                                Agregar Producto
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex flex-col md:flex-row items-center justify-between pt-8 border-t border-slate-100 dark:border-slate-800 mt-8 gap-6">
                        <div class="flex items-baseline gap-3">
                            <span class="text-slate-500 font-medium">Total del Pedido:</span>
                            <span class="text-3xl font-bold text-primary">{{ formatCurrency(totalOrder) }}</span>
                        </div>
                        <div class="flex items-center gap-4 w-full md:w-auto">
                            <button @click="cancel" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-8 py-3 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-all font-semibold" type="button">
                                <span class="material-symbols-outlined text-[20px]">close</span>
                                Cancelar
                            </button>
                            <button 
                                :disabled="isLoading"
                                class="flex-1 md:flex-none flex items-center justify-center gap-2 px-10 py-3 bg-primary text-white rounded-lg hover:bg-teal-700 transition-all font-semibold shadow-md shadow-primary/20 disabled:opacity-70 disabled:cursor-not-allowed" 
                                type="submit"
                            >
                                <span v-if="!isLoading" class="material-symbols-outlined text-[20px]">save</span>
                                {{ isLoading ? 'Guardando...' : 'Guardar Pedido' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
