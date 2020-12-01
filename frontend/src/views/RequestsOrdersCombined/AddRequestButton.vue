<template>
  <div class="add__request">
    <v-menu offset-y>
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          outlined
          dense
          small
          v-bind="attrs"
          v-on="on"
        >
          ADD
        </v-btn>
      </template>
      <v-list>
        <v-list-item @click="openUploadOrdersDialog = true">
          <v-list-item-title>Upload Order(s)</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
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
