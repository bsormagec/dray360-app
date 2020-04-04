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

    <OrdersCreateUpload
      :files="files"
      :handle-drop="handleDrop"
    />

    <OrdersCreateSubmitted
      :files="files"
      :delete-file="deleteFile"
    />

    <v-btn
      color="primary"
      :style="{ marginLeft: 'auto', marginTop: '1.1rem' }"
    >
      create order
    </v-btn>
  </div>
</template>

<script>
import OrdersCreateUpload from '@/views/Orders/OrdersCreateUpload'
import OrdersCreateSubmitted from '@/views/Orders/OrdersCreateSubmitted'

export default {
  name: 'OrdersCreate',

  components: {
    OrdersCreateUpload,
    OrdersCreateSubmitted
  },

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
</style>
