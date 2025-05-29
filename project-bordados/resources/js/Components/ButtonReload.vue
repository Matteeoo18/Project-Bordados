<script setup>
import { router } from '@inertiajs/vue3';

const props = defineProps({
    link: String,
})

const limpiarFills = () =>{
    router.get(route(props.link));
};

</script>

<template>
    <button class="bg-indigo-600 p-2 text-white text-sm mt-1 rounded-lg" @click="limpiarFills"><i class="fa fa-refresh"
            aria-hidden="true"></i></button>
</template>
