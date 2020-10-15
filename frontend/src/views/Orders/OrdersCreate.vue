<template>
  <div class="create">
    <div class="create__close">
      <v-icon
        :style="{ position: 'absolute', top: '24px', left: '15' }"
        @click="toggleMobileSidebar"
      >
        mdi-menu
      </v-icon>
    </div>

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
      :add-files="addFiles"
      :delete-all="deleteAll"
    />

    <OrdersCreateSubmitted
      :files="files"
      :delete-file="deleteFile"
    />

    <v-btn
      color="primary"
      class="submit-order-btn"
      :style="{ marginLeft: 'auto', marginTop: '11px' }"
      @click="createOrder()"
    >
      create order
    </v-btn>
  </div>
</template>

<script>
import OrdersCreateUpload from '@/views/Orders/OrdersCreateUpload'
import OrdersCreateSubmitted from '@/views/Orders/OrdersCreateSubmitted'
import orders, { types } from '@/store/modules/orders'
import { mapActions } from 'vuex'
import { reqStatus } from '@/enums/req_status'

export default {
  name: 'OrdersCreate',

  components: {
    OrdersCreateUpload,
    OrdersCreateSubmitted
  },

  props: {
    toggleMobileSidebar: {
      type: Function,
      required: true
    }
  },

  data: () => ({
    files: []
  }),

  methods: {
    ...mapActions(orders.moduleName, [types.postUploadPDF]),

    deleteFile (file) {
      this.files = this.files.filter(f => f.name !== file.name)
    },

    deleteAll () {
      this.files = []
    },

    addFiles (newFiles) {
      const filtered = [...this.files, ...newFiles].filter(f => f.type === 'application/pdf')
      const unique = []
      const uniqueNames = []

      filtered.forEach(fil => {
        if (uniqueNames.includes(fil.name)) return
        uniqueNames.push(fil.name)
        unique.push(fil)
      })

      this.files = unique
    },

    async uploadFile (file) {
      const status = await this[types.postUploadPDF](file)

      if (status === reqStatus.success) {
        console.log('upload file success')
      } else {
        console.log('error uploading file')
      }
    },

    createOrder () {
      console.log('vc.files: ', this.files)
      if (this.files.length === 0) {
        alert('Please select a PDF to upload first')
        return
      }

      this.files.forEach(file => this.uploadFile(file))
      this.files = []
    }
  }
}
</script>

<style lang="scss" scoped>
.create {
  display: flex;
  flex-direction: column;
  width: 100%;
  padding: rem(50) rem(10);
  overflow-x: hidden;

  @media screen and (min-width: map-get($breakpoints, med)) {
    min-width: 25%;
    max-width: 25%;
    box-shadow: map-get($properties, inset-shadow-left);
    border-left: rem(1) solid map-get($colors, grey-2);
    padding: rem(52) rem(16);
    padding-bottom: rem(30);
  }

  @media screen and (min-width: map-get($breakpoints, lg)) {
    padding: rem(52) rem(36);
  }
}

.create__close {
  @media screen and (min-width: map-get($breakpoints, med)) {
    display: none;
  }
}

.create__header {
  display: flex;
  width: 100%;
  border-bottom: rem(2) solid map-get($colors, grey-2);
}

.header__btn {
  margin: 0 auto;
  margin-bottom: rem(32);
  font-size: rem(12);
}

.submit-order-btn::v-deep .v-btn__content {
  font-size: rem(12);
}
</style>
