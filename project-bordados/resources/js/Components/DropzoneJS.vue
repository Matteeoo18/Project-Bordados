<template>

  <form id="my-dropzone" class="dropzone">
  </form>
</template>


<script setup>
import Dropzone from "dropzone";
import 'dropzone/dist/dropzone.css';
import { onMounted } from "vue";

const emit = defineEmits(['file']);

onMounted(() => {
  Dropzone.autoDiscover = false;

  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  const myDropzone = new Dropzone("#my-dropzone", {
    url: route('upload.archivo'),
    acceptedFiles: "image/*, video/*",
    headers: {
      'X-CSRF-TOKEN': csrfToken,
    },
    dictDefaultMessage: "Arrastre los archivos aqui o haga clic",
    chunking: true,
    retryChunks: true, // Se usa por si un chunk falla se vuelva a intentar
    maxFilesize: 1 * 1024 * 1024,
    paramName:'archivo',
    addRemoveLinks: true,
    maxFiles: 1
  });

  myDropzone.on("error",(file, errorMessage, xhr)=> {
    console.log("ERROR: ", errorMessage);
  });


  myDropzone.on("addedfile", file => {
    emit('file', file);
  });
});


</script>

<style scoped>
.dropzone {
  text-align: center;
  align-items: center;
}
.dz-remove{
  color: blue;
}
</style>