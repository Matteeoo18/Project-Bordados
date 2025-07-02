<template>
    <div class="card justify-content-center align-items-center discontinuous-border bg-light">
      <div class="card-body text-center">
        <div>
          <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="#CDCDCD" class="bi bi-cloud-arrow-up" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M7.646 5.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0V6.707L6.354 7.854a.5.5 0 1 1-.708-.708l2-2z"/>
            <path d="M4.406 3.342A5.53 5.53 0 0 1 8 2c2.69 0 4.923 2 5.166 4.579C14.758 6.804 16 8.137 16 9.773 16 11.569 14.502 13 12.687 13H3.781C1.708 13 0 11.366 0 9.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383zm.653.757c-.757.653-1.153 1.44-1.153 2.056v.448l-.445.049C2.064 6.805 1 7.952 1 9.318 1 10.785 2.23 12 3.781 12h8.906C13.98 12 15 10.988 15 9.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 4.825 10.328 3 8 3a4.53 4.53 0 0 0-2.941 1.1z"/>
          </svg>
        </div>
        <div v-bind="getRootProps()">
          <input v-bind="getInputProps()">
          <p v-if="isDragActive">Soltar archivo aquí...</p>
          <p v-else>Arrastrar y soltar archivo aquí</p>
          <!-- <div v-if="isFocused" id="focus">
            focused
          </div>
          <div v-if="isDragReject" id="isDragReject">
            isDragReject: {{ isDragReject }}
          </div> -->
        </div>
        <span @click="open" class="btn btn-outline-secondary">Seleccionar archivo</span>
      </div>
    </div>
    <div v-if="acceptedFiles.length > 0">
      <div v-for="file in acceptedFiles">
        <div class="d-flex flex-col">
          <div v-if="file.type.startsWith('image/')" class="p-2 image-container">
            <img class="iconType" :src="fileType([file])">
          </div>
          <div v-else class="p-2">
            <img class="iconType" :src="fileType([file])">
          </div>
          <div class="p-2">
            {{ fileName([file]) }}
          </div>
        </div>
      </div>
    </div>
</template>

<script setup>
  import { reactive, defineEmits, defineProps } from 'vue'
  import { useDropzone } from 'vue3-dropzone'

  const emit = defineEmits();
  // const props = defineProps(['name']);

  function onDrop(acceptedFiles, rejectReasons) {
    // console.log('acceptedFiles', acceptedFiles)
    // console.log('rejectReasons', rejectReasons)
    emit('files', acceptedFiles);
  }

  function fileName(acceptedFiles){
    const file = acceptedFiles[0];

    return file.name;
  }

  function fileType(acceptedFiles){
    const file = acceptedFiles[0];

    if (file.type == 'application/pdf'){
      return '/images/pdf.png';
    }
    else if (file.type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'){
      return '/images/word.png';
    }
    else {
      return URL.createObjectURL(file);
    }
  }

  const options = reactive({
    multiple: false,
    onDrop,
    accept: 'image/*,.mp4,.webm,.avi'
    
  })

  const {
    getRootProps,
    getInputProps,
    isDragActive,
    isFocused,
    isDragReject,
    open,
    acceptedFiles
  } = useDropzone(options)
</script>

<script>
  export default {
    emits: ['files'],
    // props: {
    //   name: '',
    // }
  }
</script>

<style>
  .discontinuous-border {
    border: 2px dashed  #CDCDCD;
  }

  .iconType {
    width: 50px;
    height: 50px;
    border-radius: 5px;
  }

  .image-container {
    position: relative;
    overflow: hidden;
    border-radius: 5px;
    transition: transform 0.3s;
  }

  .image-container:hover img {
    transform: scale(2);
  }
</style>
