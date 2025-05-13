<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight text-center">
                Usuarios registrados
            </h2>
        </template>


        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                        
                        <label class="block mb-4 columns-3xs">
                            <span class="sr-only">Buscar</span>
                            <input id="search" type="text" placeholder="Buscar..."
                                v-model="inputFill"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                            <button class="bg-indigo-600 p-2 text-white text-sm mt-1 rounded-lg mx-4" @click="filtrarUsuarios">Buscar</button>
                            <button class="bg-indigo-600 p-2 text-white text-sm mt-1 rounded-lg" @click="limpiarFills">Limpiar filtros</button>
                        </label>
                    </div>
                    <div class="w-full max-w-4xl bg-white shadow-xl rounded-2xl  mx-auto my-10">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-indigo-600 text-center text-white">
                                    <tr>
                                        <th class="px-6 py-3 uppercase tracking-wider">Nombre</th>
                                        <th class="px-6 py-3 uppercase tracking-wider">Correo</th>
                                        <th class="px-6 py-3 uppercase tracking-wider">Rol</th>
                                        <th class="px-6 py-3 uppercase tracking-wider">Estado</th>
                                        <th class="px-6 py-3 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-gray-700">
                                    <tr v-for="usuario in userData.data" :key="usuario.id" class="text-center">
                                        <td class="px-6 py-4">{{ usuario.name }}</td>
                                        <td class="px-6 py-4">{{ usuario.email }}</td>
                                        <td class="px-6 py-4">{{ usuario.roles.nombre_rol }}</td>
                                        <td class="px-6 py-4" v-if="usuario.is_active">Activo</td>
                                        <td class="px-6 py-4" v-else>Inactivo</td>
                                        <td class="px-6 py-4">
                                            <inertia-link class="botones bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                                                1ra accion
                                            </inertia-link>
                                            <inertia-link class="botones bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition ms-5">
                                                2da accion
                                            </inertia-link>
                                        </td> 
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { route } from 'ziggy-js';
import { defineComponent } from 'vue';

export default defineComponent({
    props: {
        usuarios: Object
    },
    components: {
        AppLayout
    },
    data(){
        return{
            inputFill: '',
            userData: this.usuarios
        }
    },
    methods: {
        filtrarUsuarios(){
            if (this.inputFill != ""){

                axios.post(route('usuarios.fill'), {'dataFill': this.inputFill}).then(res=>{
                    this.userData = res.data.user;
                })
            }
        },
        limpiarFills(){
            this.$inertia.get(route("usuarios.index"));
        }
    }
});
</script>
<style>
    .botones:hover{
        cursor: pointer;
    }
</style>