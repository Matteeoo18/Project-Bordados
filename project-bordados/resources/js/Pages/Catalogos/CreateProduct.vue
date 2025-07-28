<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { reactive, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import Dropzone from '@/Components/DropzoneJS.vue'
import { route } from 'ziggy-js'
import InputError from '@/Components/InputError.vue'

const archivo = ref(null)
const tag = ref('bordado')
const errors = ref({})
const loading = ref(false)

const form = reactive({
    titulo: '',
    descripcion: ''
})


const filesU = (fileData) => {
    archivo.value = fileData
    if (fileData) {
        console.log("Archivo completo:", archivo.value)
    } else {
        console.log("Archivo eliminado, archivo.value ahora es:", archivo.value)
    }
}

// Validación simple
const validator = () => {
    let valido = true
    errors.value.general = ''

    if (form.titulo.trim() === '' || form.descripcion.trim() === '') {
        errors.value.general = 'Los campos título y descripción son obligatorios'
        valido = false
    }

    if (!archivo.value) {
        errors.value.archivo = 'Se debe cargar un archivo.'
        valido = false
    }

    return valido
}

// Enviar datos al backend (ya con archivo previamente subido por Dropzone)
const crearProducto = () => {
    if (!validator()) return;

    const formData = new FormData();
    formData.append('titulo', form.titulo);
    formData.append('descripcion', form.descripcion);
    formData.append('ruta', archivo.value.ruta);
    formData.append('nombre', archivo.value.nombre);
    formData.append('mime', archivo.value.mime);

    router.post(route('catalogo.store'), formData, {
        forceFormData: true,
        onError: (err) => {
            errors.value = err;
            loading.value = false;
        },
        onFinish: () => {
            form.titulo = '';
            form.descripcion = '';
            archivo.value = null;
            loading.value = false;
        },
    });
};

</script>

<template>
    <AppLayout title="Crear Producto">
        <div class="py-12">
            <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
                <h2 class="text-2xl font-bold mb-4">Crear Producto</h2>

                <div v-if="errors.general">
                    <InputError :message="errors.general"></InputError>
                </div>

                <form @submit.prevent="crearProducto" class="space-y-4">
                    <div>
                        <label class="block text-gray-700">Título <span class="text-red-600">*</span></label>
                        <input v-model="form.titulo" name="titulo" type="text" class="w-full border rounded p-2" />
                        <div v-if="errors.titulo">
                            <InputError :message="errors.titulo"></InputError>
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700">Imagen o video<span class="text-red-600">*</span></label>
                        <p><span class="text-yellow-600 font-bold">¡IMPORTANTE! </span>asegúrate de que no supere los
                            100 MB.</p>
                        <Dropzone @file="filesU"></Dropzone>
                        <div v-if="errors.archivo">
                            <InputError :message="errors.archivo"></InputError>
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700">Descripción <span class="text-red-600">*</span></label>
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
