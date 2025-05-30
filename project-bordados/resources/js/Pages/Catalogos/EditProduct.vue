<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import Dropzone from '@/Components/Dropzone.vue'

// Accedemos a las props
const props = defineProps({
    producto: Object,
})

// Variables reactivas con los valores actuales del producto
const titulo = ref(props.producto.titulo_post)
const enlace = ref(props.producto.enlace_post)
const descripcion = ref(props.producto.descripcion_post)
const archivo = ref(null)

// Función para actualizar el producto
const actualizarProducto = () => {
    const formData = new FormData()

    formData.append('titulo_post', titulo.value)
    formData.append('descripcion_post', descripcion.value)

    // Si hay un archivo nuevo, se incluye
    if (archivo.value) { 
        if((archivo.value.size/1048576)<100){ //Aqui se esta evaluando que el tamaño sea menor a 100 megas, se dvide entre 1048576 porque el valor esta en bytes (Lo pasamos a MB)
            console.log("Si se puede enviar el archivo");
            formData.append('enlace_post', archivo.value)
        }else{
            alert("El tamaño del archivo excede las 100 MB, no lo podemos enviar.");
        }

    } else {
        // Si no hay archivo nuevo, puedes enviar la URL existente si lo necesitas
        formData.append('enlace_post', enlace.value)
    }
    // para mirar si estamos enviando los datos correctamente
    // for (let [key, value] of formData.entries()) {
    //     console.log(`${key}:`, value)
    // }
    router.post(route('catalogo.update', props.producto.id), formData, {
        forceFormData: true, // Inertia lo requiere para que no serialice como JSON
        preserveScroll: true,
    })
}

const filesU = (files) => {
    archivo.value = files[0]
}
// Función para verificar si es video
const isVideo = (url) => {
    return /\.(mp4|webm|ogg)$/i.test(url)
}

// Función para verificar si es imagen
const isImage = (url) => {
    return /\.(jpg|jpeg|png|gif|webp)$/i.test(url)
}
const filePreview = computed(() => {
    if (!archivo.value) return null
    return URL.createObjectURL(archivo.value)
})
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

                    <div class="mt-4">
                        <template v-if="archivo">
                            <!-- Mostrar preview del archivo nuevo -->
                            <template v-if="isVideo(archivo.name)">
                                <video :src="filePreview" controls class="w-full rounded-lg" />
                            </template>
                            <template v-else-if="isImage(archivo.name)">
                                <img :src="filePreview" alt="Vista previa" class="w-full rounded-lg" />
                            </template>
                        </template>

                        <template v-else>
                            <!-- Mostrar archivo de la url antigua -->
                            <div v-if="isVideo(enlace)">
                                <video :src="enlace" controls class="w-full rounded-lg" />
                            </div>
                            <span v-else-if="isImage(enlace)">
                                <img :src="enlace" alt="Vista previa" class="w-full rounded-lg" />
                            </span>
                            <span v-else>
                                <p class="text-red-500">El contenido recibido es inválido</p>
                            </span>
                        </template>
                    </div>

                    <div>
                        <label class="block text-gray-700">Imagen o video</label>
                        <!-- <input type="file" accept="image/*,video/*" @change="select_file" class="w-full border rounded p-2" /> -->
                        <p><span class="text-yellow-600 font-bold">¡IMPORTANTE! </span>antes de seleccionar el archivo
                            asegurese que no
                            supere las 100 MB.</p>
                        <Dropzone @files="filesU"></Dropzone>

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