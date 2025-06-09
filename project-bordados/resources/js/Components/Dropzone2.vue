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
    url: route('upload.archivo'),
    acceptedFiles: "image/*, video/*",
    headers: {
      'X-CSRF-TOKEN': csrfToken,
    },
    chunking: true,
    maxFilesize: 500,
    paramName:'archivo'
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