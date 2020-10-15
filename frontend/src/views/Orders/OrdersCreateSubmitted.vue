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

      <v-btn
        icon
        @click="deleteFile(file)"
      >
        <v-icon
          color="red"
        >
          mdi-delete
        </v-icon>
      </v-btn>
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
  border-bottom: rem(1) solid map-get($colors, grey-2);
  margin-bottom: rem(17);
  font-size: rem(16);
  font-weight: 700;
  letter-spacing: 0;
}

.submitted__file {
  display: flex;
  align-items: center;
  margin-bottom: rem(24);

  span {
    margin-left: rem(7);
    margin-right: rem(19);
    font-size: rem(14);
    font-weight: bold;
    color: var(--v-primary-base);
    text-decoration-line: underline;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
}

</style>
