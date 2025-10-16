<template>
  <form id="my-dropzone" class="dropzone"></form>
</template>

<script setup>
import Dropzone from "dropzone";
import "dropzone/dist/dropzone.css";
import { onMounted, onBeforeUnmount } from "vue";

const emit = defineEmits(["file"]);

let uploadedFileData = null; // 🧠 Guardará la info del archivo subido para poder eliminarlo después

onMounted(() => {
  Dropzone.autoDiscover = false;

  const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

  const myDropzone = new Dropzone("#my-dropzone", {
    url: route("upload.archivo"),
    acceptedFiles: "image/*, video/*",
    headers: {
      "X-CSRF-TOKEN": csrfToken,
    },
    dictDefaultMessage: "Arrastre los archivos aqui o haga clic",
    chunking: true,
    retryChunks: true,
    maxFilesize: 1 * 1024 * 1024,
    paramName: "archivo",
    addRemoveLinks: true,
    maxFiles: 1,
  });

  myDropzone.on("error", (errorMessage) => {
    console.log("ERROR: ", errorMessage);
  });

  myDropzone.on("success", (file, response) => {
    console.log("Archivo subido con éxito:", response);

    // ✨ Limpiar el nombre
    const sanitizedName = file.name.replace(/\s+/g, "_").toLowerCase();

    // ✨ Crear nuevo objeto File con nombre limpio
    const cleanFile = new File([file], sanitizedName, { type: file.type });

    // 🧠 Guardamos la info del archivo subido (para poder eliminarlo si se cierra la página)
    uploadedFileData = {
      ruta: response.path,
      nombre: sanitizedName,
    };

    // 🔥 Emitimos al componente padre
    emit("file", {
      file: cleanFile,
      ruta: response.path,
      nombre: sanitizedName,
      mime: response.mime_type,
    });
  });

  // 🔥 Cuando el usuario elimina manualmente el archivo
  myDropzone.on("removedfile", (file) => {
    console.log("Archivo eliminado visualmente:", file);

    if (uploadedFileData) {
      fetch(route("dropzone.eliminar"), {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify(uploadedFileData),
      })
        .then((res) => res.json())
        .then((data) => {
          console.log("Servidor eliminó el archivo:", data);
        })
        .catch((error) => {
          console.error("Error al eliminar en backend:", error);
        });

      uploadedFileData = null;
    }

    emit("file", null); // Limpia el archivo en el componente padre
  });

  // 🧹 Si el usuario cierra o recarga la página sin guardar
  window.addEventListener("beforeunload", async (e) => {
    if (uploadedFileData) {
      await fetch(route("dropzone.eliminar"), {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify(uploadedFileData),
      });
      uploadedFileData = null;
    }
  });
});

// 🧼 Limpieza del evento al desmontar el componente
onBeforeUnmount(() => {
  window.removeEventListener("beforeunload", () => { });
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
