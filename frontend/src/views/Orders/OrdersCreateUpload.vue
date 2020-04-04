<template>
  <div class="upload">
    <h1 class="upload__title">
      CREATE ORDER BY UPLOAD
    </h1>
    <h2 class="upload__subtitle">
      Upload Documents
    </h2>

    <div class="upload__input">
      <v-file-input
        v-model="files"
        multiple
        solo
        dense
        hide-details
        append
        :accept="accept"
        label="File input"
      />

      <span class="input__legend">
        <strong>Supported file type:</strong> PDF
      </span>
    </div>

    <div
      class="upload__area"
      @dragenter.prevent.stop
      @dragover.prevent.stop
      @dragleave.prevent.stop
      @drop.prevent.stop="handleDrop"
    >
      <span class="area__legend">... or drag documents here to upload</span>
      <v-file-input
        v-model="files"
        multiple
        :accept="accept"
      />
    </div>
  </div>
</template>

<script>
export default {
  name: 'OrdersCreateUpload',

  props: {
    files: {
      type: Array,
      required: true
    },
    handleDrop: {
      type: Function,
      required: true
    }
  },

  data: () => ({
    accept: '.pdf'
  })
}
</script>

<style lang="scss" scoped>
.upload {
  display: flex;
  flex-direction: column;
  padding-top: 2.8rem;
  margin-bottom: 3.4rem;
}

.upload__title {
  font-size: 2.1rem;
  line-height: 2.4rem;
  margin-bottom: 3.4rem;
}

.upload__subtitle {
  line-height: 1.8rem;
  margin-bottom: 0.9rem;
}

.upload__input {
  margin-bottom: 2rem;
}

.upload__input .input__legend {
  display: block;
  margin-top: 0.8rem;
  color: map-get($colors, grey-4);
}

.upload__area {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 9.4rem;
  background-color: map-get($colors, grey-5);
  border: 0.2rem dashed map-get($colors, grey-3);
  border-radius: 0.5rem;
}

.area__legend {
  display: block;
  font-size: 1.4rem !important;
  font-weight: bold;
  color: map-get($colors, grey-4);
}
</style>
