<template>

  <form id="my-dropzone" class="dropzone">


  </form>
</template>


<script setup>
import Dropzone from "dropzone";
import 'dropzone/dist/dropzone.css';
import { onMounted } from "vue";

onMounted(() => {
  Dropzone.autoDiscover = false;

  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  const myDropzone = new Dropzone("#my-dropzone", {
    url: "/catalogo/update", //Se debe de cambiar la ruta
    acceptedFiles: "image/*, video/*",
    headers: {
      'X-CSRF-TOKEN': csrfToken,
    },
    dictDefaultMessage: "Arrastre los archivos aqui o haga clic",
    chunking: true,
    retryChunks: true, // Se usa por si un chunk falla se vuelva a intentar
    method: 'put',
    maxFilesize: 1 * 1024 * 1024,
  });

  myDropzone.on("error",(file, errorMessage, xhr)=> {
    console.log("ERROR: ", errorMessage);
  }) 
});
</script>

<style scoped>
.dropzone {
  text-align: center;
  align-items: center;
}
</style>