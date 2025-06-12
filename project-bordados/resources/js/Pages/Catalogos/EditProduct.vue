<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import Dropzone from '@/Components/DropzoneJS.vue'

// Accedemos a las props
const props = defineProps({
    producto: Object,
})

// Constantes
const BYTES_IN_MB = 1048576
const MAX_MB = 40

// Estado reactivo
const titulo = ref(props.producto.titulo_post)
const enlace = ref(props.producto.enlace_post)
const descripcion = ref(props.producto.descripcion_post)
const archivo = ref(null)
const errorArchivo = ref('')
const cargando = ref(false)

// Validar tamaño
const validarArchivo = (file) => {
    const sizeInMB = file.size / BYTES_IN_MB
    if (sizeInMB > MAX_MB) {
        errorArchivo.value = `❌ El archivo pesa ${sizeInMB.toFixed(2)} MB. Límite: ${MAX_MB} MB.`
        return false
    }
    errorArchivo.value = ''
    return true
}

// Subida
const actualizarProducto = () => {
    if (cargando.value) return
    cargando.value = true

    const formData = new FormData()
    formData.append('titulo_post', titulo.value)
    formData.append('descripcion_post', descripcion.value)

    if (archivo.value) {
        if (!validarArchivo(archivo.value)) {
            cargando.value = false
            return
        }
        formData.append('enlace_post', archivo.value)
    } else {
        formData.append('enlace_post', enlace.value)
    }

    router.post(route('catalogo.update', props.producto.id), formData, {
        forceFormData: true,
        preserveScroll: true,
        onFinish: () => {
            cargando.value = false
        }
    })
}

// Recibir archivos del dropzone
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
                        <p><span class="text-yellow-600 font-bold">¡IMPORTANTE! </span>antes de seleccionar el archivo
                            asegurese que no
                            supere las 100 MB.</p>
                        <Dropzone></Dropzone>
                    </div>

                    <div>
                        <label class="block text-gray-700">Descripción</label>
                        <textarea v-model="descripcion" class="w-full border rounded p-2"></textarea>
                    </div>

                    <p v-if="errorArchivo" class="text-red-600 text-sm">{{ errorArchivo }}</p>

                    <button :disabled="cargando" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        {{ cargando ? 'Actualizando...' : 'Actualizar' }}
                    </button>
                </form>
            </div>
        </div>
    </AppLayout>
</template>