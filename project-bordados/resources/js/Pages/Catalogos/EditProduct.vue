<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

// Accedemos a las props
const props = defineProps({
    producto: Object
})

// Variables reactivas con los valores actuales del producto
const titulo = ref(props.producto.titulo_post)
const enlace = ref(props.producto.enlace_post)
const descripcion = ref(props.producto.descripcion_post)

// Función para actualizar el producto
const actualizarProducto = () => {
    router.put(route('catalogo.update', props.producto.id), {
        titulo_post: titulo.value,
        enlace_post: enlace.value,
        descripcion_post: descripcion.value
    })
}
</script>

<template>
    <AppLayout title="Editar Producto">
        <div class="py-12">
            <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
                <h2 class="text-2xl font-bold mb-4">Editar Producto</h2>

                <form @submit.prevent="actualizarProducto" class="space-y-4">
                    <div>
                        <label class="block text-gray-700">Título</label>
                        <input v-model="titulo" type="text" class="w-full border rounded p-2" />
                    </div>

                    <div>
                        <label class="block text-gray-700">Enlace</label>
                        <input v-model="enlace" type="text" class="w-full border rounded p-2" />
                    </div>

                    <div>
                        <label class="block text-gray-700">Descripción</label>
                        <textarea v-model="descripcion" class="w-full border rounded p-2"></textarea>
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Actualizar
                    </button>
                </form>
            </div>
        </div>
    </AppLayout>
</template>