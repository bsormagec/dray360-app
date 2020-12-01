<template>
  <div class="orders-create-upload">
    <h1 class="upload__title">
      CREATE ORDER BY UPLOAD
    </h1>
    <h2 class="upload__subtitle">
      Upload Documents
    </h2>

    <div class="upload__input">
      <v-file-input
        :rules="rules"
        multiple
        solo
        dense
        append
        label="File input"
        :value="files"
        :accept="accept"
        @change="(files) => addFiles(files)"
        @click:clear="deleteAll()"
      />

      <span class="input__legend">
        <strong>Supported file type:</strong> PDF, XLSX, CSV
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
        :rules="rules"
        multiple
        :accept="accept"
        :value="files"
        @change="(files) => addFiles(files)"
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
    addFiles: {
      type: Function,
      required: true
    },
    deleteAll: {
      type: Function,
      required: true
    }
  },

  data: () => ({
    accept: '.pdf,.xlsx,.csv,.edi',
    rules: [
      files => files.length <= 20 || 'File limit exceeded. Please upload less than 20 files.'
    ]
  }),

  methods: {
    handleDrop (e) {
      const dt = e.dataTransfer
      this.addFiles(dt.files)
    }
  }
}
</script>

<style lang="scss" scoped>
.orders-create-upload {
  display: flex;
  flex-direction: column;
  padding-top: rem(28);
  margin-bottom: rem(34);
}

.upload__title {
  font-size: rem(21);
  line-height: rem(24);
  margin-bottom: rem(34);
  font-weight: 700;
  letter-spacing: 0;
}

.upload__subtitle {
  font-size: rem(16);
  line-height: rem(18);
  margin-bottom: rem(9);
  font-weight: 700;
  letter-spacing: 0;
}

.upload__input {
  margin-bottom: rem(20);
}

.upload__input::v-deep .v-label {
  font-size: rem(12);
}

.upload__input .input__legend {
  display: block;
  margin-top: rem(8);
  color: map-get($colors, grey-4);
  font-size: rem(12);
}

.upload__area {
  display: flex;
  align-items: center;
  justify-content: center;
  height: rem(94);
  background-color: map-get($colors, grey-5);
  border: rem(2) dashed map-get($colors, grey-3);
  border-radius: rem(5);
}

.area__legend {
  display: block;
  font-size: rem(14) !important;
  font-weight: 700;
  color: map-get($colors, grey-4);

  @include media("med") {
    font-size: rem(12) !important;
  }
}
</style>
