<template>
  <div class="create">
    <div class="create__header">
      <v-btn
        class="header__btn"
        color="primary"
        outlined
      >
        Create Order Manually
      </v-btn>
    </div>

    <div class="create__upload">
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
          accept=".pdf"
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
          accept=".pdf"
        />
      </div>
    </div>

    <div class="create__submitted">
      <h2 class="submitted__title">
        Submitted Documents
      </h2>

      <div
        v-for="(file, i) in files"
        :key="i"
        class="submitted__file"
      >
        <v-icon>mdi-file-outline</v-icon>
        <span>{{ file.name }}</span>
        <v-icon
          color="red"
          @click="deleteFile(file)"
        >
          mdi-delete
        </v-icon>
      </div>
    </div>

    <v-btn
      color="primary"
      :style="{ marginLeft: 'auto', marginTop: '1.1rem' }"
    >
      create order
    </v-btn>
  </div>
</template>

<script>
export default {
  name: 'OrdersCreate',

  data: () => ({
    files: []
  }),

  methods: {
    deleteFile (file) {
      this.files = this.files.filter(f => f.name !== file.name)
    },

    handleDrop (e) {
      const dt = e.dataTransfer
      this.files = [...dt.files].filter(f => f.name.includes('.pdf'))
    }
  }
}
</script>

<style lang="scss" scoped>
.create {
  display: flex;
  flex-direction: column;
  width: 27%;
  padding: 5.2rem 3.6rem;
  padding-bottom: 3rem;
  box-shadow: map-get($properties, inset-shadow-left);
  border-left: 0.1rem solid map-get($colors, grey-2);
}

.create__header {
  display: flex;
  width: 100%;
  border-bottom: .2rem solid map-get($colors, grey-2);
}

.header__btn {
  margin: 0 auto;
  margin-bottom: 3.2rem;
}

.create__upload {
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

.submitted__title {
  border-bottom: 0.1rem solid map-get($colors, grey-2);
  margin-bottom: 1.7rem;
}

.submitted__file {
  display: flex;
  align-items: center;
  margin-bottom: 2.4rem;

  span {
    margin-left: 0.7rem;
    margin-right: 1.9rem;
    font-size: 1.4rem !important;
    font-weight: bold;
    color: map-get($colors, blue);
    text-decoration-line: underline;
  }
}
</style>
