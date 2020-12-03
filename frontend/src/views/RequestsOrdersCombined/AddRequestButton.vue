<template>
  <div class="add__request">
    <v-btn
      outlined
      dense
      small
      @click="openUploadOrdersDialog = true"
    >
      ADD
    </v-btn>
    <UploadOrdersDialog
      :open="openUploadOrdersDialog"
      @close="openUploadOrdersDialog = false"
      @uploaded="handleFilesUploaded"
    />
  </div>
</template>
<script>
import UploadOrdersDialog from './UploadOrdersDialog'

export default {
  name: 'AddRequestButton',
  components: { UploadOrdersDialog },
  data: (vm) => ({
    openUploadOrdersDialog: false
  }),
  methods: {
    handleFilesUploaded () {
      this.openUploadOrdersDialog = false
      this.$emit('change')
    }
  }
}
</script>
<style lang="scss" scoped>
.add__request {
    align-self: center;
    margin-left: auto;
    margin-right: rem(16);
  }
</style>
