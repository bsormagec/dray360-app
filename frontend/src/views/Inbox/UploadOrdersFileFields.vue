<template>
  <div
    class="d-flex flex-column"
    style="position: relative;"
  >
    <div class="upload__input">
      <v-file-input
        dense
        outlined
        multiple
        hide-details
        label="Select Documents"
        :value="files"
        :accept="accept"
        @change="handleChange"
        @click:clear="$emit('clear')"
      />

      <span class="input__legend">
        <strong>Supported file types:</strong> {{ accept.toUpperCase() }}
        <br>
        <strong>Uploading XLSX?</strong> Use one of these <a
          href="https://dray360-site-assets.s3.us-east-2.amazonaws.com/order_template_spreadsheets/order_template_spreadsheets.zip"
          target="_blank"
        >templates</a>
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
        multiple
        :accept="accept"
        :value="files"
        @change="handleChange"
      />
    </div>
  </div>
</template>

<script>
export default {
  name: 'UploadOrdersFileFields',

  props: {
    files: {
      type: Array,
      required: true
    },
    accept: {
      type: String,
      required: false,
      default: ''
    }
  },

  methods: {
    handleChange (files) {
      this.$emit('change', files)
    },
    handleDrop (e) {
      const dt = e.dataTransfer
      this.handleChange(dt.files)
    }
  }
}
</script>

<style lang="scss" scoped>
.upload__input {
  margin-bottom: rem(20);
}

.input__legend {
  display: block;
  margin: rem(8) 0 0 rem(12);
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
