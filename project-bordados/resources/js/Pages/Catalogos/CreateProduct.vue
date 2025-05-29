<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { reactive, ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import Dropzone from '@/Components/Dropzone.vue'
import axios from 'axios'
import { route } from 'ziggy-js'
import InputError from '@/Components/InputError.vue'


const archivo = ref(null)
const tag = ref('bordado')
const errors = ref({})
const loading = ref(false)
// Definir formulario reactivo
const form = reactive({
    titulo: '',
    descripcion: ''
})

const cloudData = reactive({
    //signature: '', Por el momento no se necesita
    //timestamp: '', Por el momento no se necesita
    uplpreset: '',
    //apik: '', Por el momento no se necesita
    cloudName: ''
})

const filesU = (files) => {
    archivo.value = files[0]
}

//  Enviar formulario con Inertia 
const crearProducto = (url, publicId, tag, res_type) => {
    // console.log(form);
    const formData = new FormData()
    formData.append('titulo', form.titulo)
    formData.append('descripcion', form.descripcion)
    formData.append('enlace_post', url)
    formData.append('public_id', publicId)
    formData.append('tag_post', tag)
    formData.append('type_post', res_type)
    // console.log(formData);

    // Enviar el formulario usando Inertia
    router.post(route('catalogo.store'), formData, {
        forceFormData: true, // Asegura que se envíe como multipart/form-data
        onError: (err) => {
            errors.value = err
            loading.value = true
        },
        onFinish: () => {
            // Limpiar el formulario después de enviar
            form.titulo = ''
            form.descripcion = ''
            form.archivo = null
            loading.value = false
        },

    })
}

//Se envia la imagen directamente a cloudinary y en la respuesta se llama la función para enviar al back (catalogo.store)
const sendCloudinary = () => {
    const url = `https://api.cloudinary.com/v1_1/${cloudData.cloudName}/upload`

    errors.value = {
        ...errors.value,
        archivo: ""
    }

    // console.log(archivo.value);

    loading.value = true

    if (archivo.value !== null) {
        const formData = new FormData()
        formData.append('file', archivo.value)
        formData.append('upload_preset', cloudData.uplpreset)
        formData.append('tags', form.titulo + " " + tag.value)

        axios.post(url, formData).then(res => {
            console.log(res.data);  //Se necesita la key del json "resource_type"
            crearProducto(res.data.secure_url, res.data.public_id, res.data.tags[0], res.data.resource_type)
        }).catch(error => {
            console.error('Error al hacer la petición:', error.data)
            errors.value.archivo = "No se ha podido almacenar el archivo, asegurese que el tamaño no supere 100 MB."
            loading.value = false
        })
    } else {
        errors.value.archivo = "Se debe de agregar un archivo de imágen o video."
        loading.value = false
    }
}



//Funcion para llamar la ruta de signature
const getSignature = () => {
    axios.get(route('catalogo.signature')).then(res => {
        // console.log(res.data)
        cloudData.uplpreset = res.data.upload_preset //Este es el  nombre para realizar los cambios en cloudinary
        cloudData.cloudName = res.data.cloud_name //Nombre del usuario, es con el que se identifica para poder hacer la subida de imagnes
    }).catch(error => {
        console.error('Error al hacer la petición:', error)
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

                <form @submit.prevent="sendCloudinary" class="space-y-4">
                    <div>
                        <label class="block text-gray-700">Título</label>
                        <input v-model="form.titulo" name="titulo" type="text" class="w-full border rounded p-2" />
                        <div v-if="errors.titulo">
                            <InputError :message="errors.titulo"></InputError>
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700">Imagen o video</label>
                        <!-- <input type="file" accept="image/*,video/*" @change="select_file" class="w-full border rounded p-2" /> -->
                        <p><span class="text-yellow-600 font-bold">¡IMPORTANTE! </span>antes de seleccionar el archivo asegurese que no supere las 100 MB.</p>
                        <Dropzone @files="filesU"></Dropzone>
                        <div v-if="errors.archivo">
                            <InputError :message="errors.archivo"></InputError>
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-700">Descripción</label>
                        <textarea v-model="form.descripcion" name="descripcion"
                            class="w-full border rounded p-2"></textarea>
                        <div v-if="errors.descripcion">
                            <InputError :message="errors.descripcion"></InputError>
                        </div>
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                        :disabled="loading">
                        <i v-if="loading" class="fa-solid fa-spinner fa-spin"></i>
                        <span v-else>Crear</span>
                    </button>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
