<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

// importamos inertia para poder usarlo
// Props que vienen desde Laravel
defineProps({
    productos: Object,
});

const mensaje = ref('');
const tipoMensaje = ref('success'); // 'success' o 'error'
//funcion para crear el producto
const crearProducto = () => {
    // Redirigir a la página de creación del producto con route
    console.log("Entro a crear producto");
    router.visit(route('catalogo.create'));
}
// Eliminar producto y recargar solo los productos
const eliminarProducto = (id) => {
    axios.delete(route('catalogo.destroy', { id }))
        .then(res => {
            mensaje.value = res.data.message;
            tipoMensaje.value = 'success';

            // Recargamos solo la data de productos (no toda la página)
            router.reload({ only: ['productos'] });

            // Limpiamos el mensaje después de 3 segundos
            setTimeout(() => {
                mensaje.value = '';
            }, 3000);
        })
        .catch(err => {
            mensaje.value = 'Ocurrió un error al eliminar el producto';
            tipoMensaje.value = 'error';

            // Limpiamos el mensaje después de 3 segundos
            setTimeout(() => {
                mensaje.value = '';
            }, 3000);
        });
};
// redirigir a la página de edición del producto
const editarProducto = (id) => {
    console.log("Entro a editar producto");
    // Redirigir a la página de edición del producto con route
    router.visit(route('catalogo.edit', { id }));
}
</script>

<template>
    <div>
        <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
            <!-- Creamos el boton de crear producto -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Lista de Productos</h2>
                <button @click="crearProducto"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Crear Producto
                </button>
            </div>
        </div>

        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">

            <!-- Mensaje de éxito o error -->
            <div v-if="mensaje" :class="{
                'text-green-600': tipoMensaje === 'success',
                'text-red-600': tipoMensaje === 'error'
            }" class="mb-2">
                {{ mensaje }}
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="producto in productos.data" :key="producto.id">
                        <td class="px-6 py-4">
                            <img :src="producto.enlace_post" alt="Producto" class="w-10 h-10 rounded" />
                        </td>
                        <td class="px-6 py-4">{{ producto.titulo_post }}</td>
                        <td class="px-6 py-4">{{ producto.descripcion_post }}</td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-indigo-600 hover:text-indigo-900"
                                @click="editarProducto(producto.id)">Editar</button>

                            <button @click="eliminarProducto(producto.id)"
                                class="text-red-600 hover:text-red-900 ml-2">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="mt-4 flex justify-center gap-2">
                <button v-for="link in productos.links" :key="link.label" @click="link.url && router.visit(link.url)"
                    :disabled="!link.url" v-html="link.label" :class="[
                        'px-3 py-1 rounded',
                        link.active ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700',
                        !link.url ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-100'
                    ]" />
            </div>
        </div>
    </div>
</template>
