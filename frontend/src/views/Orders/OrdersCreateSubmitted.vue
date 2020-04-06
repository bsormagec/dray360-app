<template>
  <div class="submitted">
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
</template>

<script>
export default {
  name: 'OrdersCreateSubmitted',

  props: {
    files: {
      type: Array,
      required: true
    },
    deleteFile: {
      type: Function,
      required: true
    }
  }
}
</script>

<style lang="scss" scoped>
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
