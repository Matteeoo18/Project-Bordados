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
                            <input id="search" type="text" placeholder="Buscar..." v-model="inputFill"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                            <button class="bg-indigo-600 p-2 text-white text-sm mt-1 rounded-lg ms-1 me-4"
                                @click="filtrarUsuarios"><i class="fa fa-search" aria-hidden="true"></i>
                            </button>
                            <button-reload :link="'usuarios.index'"></button-reload>
                            <div class="columns-2">
                                <label for="fillstatus">Filtrar usuarios:</label>
                                <select id="fillstatus" v-model="sFillStatus" @change="filtrarUsuarios"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option disabled>Seleccione una opción</option>
                                    <option value="true">Activos</option>
                                    <option value="false">Inactivos</option>
                                </select>
                            </div>
                        </label>

                        <div v-show="mostrar" class="bg-yellow-200">
                            <span class="text-yellow-600 mx-4">{{ message }}</span>
                        </div>
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
                                        <td class="px-6 py-4 text-green-600" v-if="usuario.is_active">Activo</td>
                                        <td class="px-6 py-4 text-red-600" v-else>Inactivo</td>
                                        <td class="px-6 py-4" v-if="usuario.is_active">
                                            <inertia-link
                                                class="botones text-red-600 px-1 py-1 rounded hover:bg-red-400 transition text-2xl"
                                                @click="updateUserStatus(usuario.id)">
                                                <i class="fa fa-times" aria-hidden="true"></i>
                                            </inertia-link>
                                        </td>
                                        <td class="px-6 py-4" v-else>
                                            <inertia-link
                                                class="botones text-green-600 px-1 py-1 rounded hover:bg-green-400 transition text-2xl"
                                                @click="updateUserStatus(usuario.id)">
                                                <i class="fa fa-check-square" aria-hidden="true"></i>
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
import ButtonReload from '@/Components/ButtonReload.vue';
import { route } from 'ziggy-js';
import { defineComponent } from 'vue';
import axios from 'axios';


export default defineComponent({
    props: {
        usuarios: Object
    },
    components: {
        AppLayout,
        ButtonReload
    },
    data() {
        return {
            inputFill: '',
            sFillStatus: '',
            userData: this.usuarios,
            mostrar: false,
            message: '',
        }
    },
    methods: {
        filtrarUsuarios() {
            if (this.inputFill != "") {

                axios.post(route('usuarios.fill'), { 'dataFill': this.inputFill }).then(res => {
                    this.userData = res.data.user;
                    this.message = res.data.message
                    this.showMessage()
                }).catch(err => {
                    this.message = "Ocurrio un error al filtrar los usuarios" 
                    this.showMessage()
                })
            } else {
                this.message = "Ingrese datos para buscar"
                this.showMessage()
            }

            if (this.sFillStatus != "") {
                axios.get(route('usuarios.fillByStatus', { status: this.sFillStatus })).then(res => {
                    console.log(res.data)
                    this.userData = res.data.user
                    this.message = res.data.message
                    this.showMessage()
                }).catch(err => {
                    this.message = "Ocurrio un error al filtrar los usuarios por el estado"
                    this.showMessage()
                })
            }
        },
        updateUserStatus(id) {
            axios.put(route('usuarios.updateStatus', { id }), { 'status': false }).then(res => {
                this.userData = res.data.usuarios
                this.message = res.data.message
                this.showMessage()

            }).catch(err => {
                this.message = "Ocurrio un error al actualizar los usuarios"
                this.showMessage()
            })
        },
        showMessage() {
            this.mostrar = true
            setTimeout(() => {
                this.mostrar = false;
            }, 3000);
        }
    }
});
</script>
<style>
.botones:hover {
    cursor: pointer;
}
</style>