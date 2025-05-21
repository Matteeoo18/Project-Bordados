<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { reactive, ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import Dropzone from '@/Components/Dropzone.vue'
import axios from 'axios'
import { route } from 'ziggy-js'


const archivo = ref(null)
// 1. Definir formulario reactivo
const form = reactive({
    titulo: '',
    descripcion: ''
})

const cloudData = reactive({
    signature: '',
    timestamp: '',
    apik: '',
    cloud_name: ''
})


// 2. Manejar selección de archivo
const select_file = (event) => {
    form.archivo = event.target.files[0]
}

const filesU = (files) => {
    archivo.value = files[0]
}

// 3. Enviar formulario con Inertia
const crearProducto = () => {
    console.log("Entro a crear producto");
    // console.log(form);
    const formData = new FormData()
    formData.append('titulo', form.titulo)
    formData.append('descripcion', form.descripcion)
    formData.append('archivo', archivo.value)
    console.log(formData);

    // Enviar el formulario usando Inertia
    router.post(route('catalogo.store'), formData, {
        forceFormData: true, // Asegura que se envíe como multipart/form-data

        onFinish: () => {
            // Limpiar el formulario después de enviar
            form.titulo = ''
            form.descripcion = ''
            form.archivo = null
        }
    })
}

//Funcion para llamar la ruta de signature
const getSignature = () => {
    axios.get(route('catalogo.signature')).then(res => {
        // console.log(res.data)
        cloudData.signature = res.data.signature
        cloudData.timestamp = res.data.timestamp
        cloudData.apik = res.data.api_key
        cloudData.cloud_name = res.data.cloud_name 
    })
}

onMounted(() => {
    getSignature()
})
</script>

<template>
    <AppLayout title="Crear Producto">
        <div class="py-12">
            <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
                <h2 class="text-2xl font-bold mb-4">Crear Producto</h2>

                <form @submit.prevent="crearProducto" class="space-y-4">
                    <div>
                        <label class="block text-gray-700">Título</label>
                        <input v-model="form.titulo" type="text" class="w-full border rounded p-2" />
                    </div>

                    <div>
                        <label class="block text-gray-700">Imagen o video</label>
                        <!-- <input type="file" accept="image/*,video/*" @change="select_file" class="w-full border rounded p-2" /> -->
                        <Dropzone @files="filesU"></Dropzone>
                    </div>

                    <div>
                        <label class="block text-gray-700">Descripción</label>
                        <textarea v-model="form.descripcion" class="w-full border rounded p-2"></textarea>
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Crear
                    </button>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
