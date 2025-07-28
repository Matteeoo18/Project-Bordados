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
    paramName: 'archivo',
    addRemoveLinks: true,
    maxFiles: 1
  });

  myDropzone.on("error", (file, errorMessage, xhr) => {
    console.log("ERROR: ", errorMessage);
  });

  myDropzone.on("success", (file, response) => {
    console.log("Archivo subido con éxito:", response);
    emit("file", {
      file: file,
      ruta: response.path,
      nombre: response.name,
      mime: response.mime_type
    });
  });

  myDropzone.on("addedfile", file => {
    emit('file', file);
  });
  // 🔥 Evento cuando el usuario elimina manualmente un archivo
  myDropzone.on("removedfile", file => {
    console.log("Archivo eliminado visualmente:", file);

    if (file.xhr) {
      try {
        const response = JSON.parse(file.xhr.response);
        const ruta = response.path; // ejemplo: "upload/image/jpeg/2025-07-30/"
        const nombre = response.name;

        fetch(route('dropzone.eliminar'), {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
          },
          body: JSON.stringify({ ruta, nombre })
        })
          .then(res => res.json())
          .then(data => {
            console.log("Servidor eliminó el archivo:", data);
          })
          .catch(error => {
            console.error("Error al eliminar en backend:", error);
          });

      } catch (e) {
        console.error("No se pudo obtener la ruta de eliminación:", e);
      }
    }

    emit("file", null); // Limpia en el componente padre
  });
});


</script>

<style scoped>
.dropzone {
  text-align: center;
  align-items: center;
}

.dz-remove {
  color: blue;
}
</style>