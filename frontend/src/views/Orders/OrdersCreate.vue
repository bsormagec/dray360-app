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

    <div v-if="shouldAskForVariantName">
      <v-text-field
        v-model="variantName"
        label="Variant Name"
        color="primary"
        outlined
        clearable
        dense
      />
    </div>

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

import { postUploadPDF } from '@/store/api_calls/orders'
import utils, { type } from '@/store/modules/utils'
import { mapActions } from 'vuex'

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
    files: [],
    variantName: ''
  }),

  computed: {
    shouldAskForVariantName () {
      for (let index = 0; index < this.files.length; index++) {
        if (this.files[index].type !== 'application/pdf') {
          return true
        }
      }
      return false
    }
  },

  methods: {
    ...mapActions(utils.moduleName, { setSnackbar: type.setSnackbar }),

    deleteFile (file) {
      this.files = this.files.filter(f => f.name !== file.name)
    },

    deleteAll () {
      this.files = []
    },

    addFiles (newFiles) {
      const acceptedMimeTypes = [
        'application/pdf',
        'text/csv',
        'text/plain',
        'application/wps-office.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/EDI-X12',
        'application/EDIFACT',
        'application/EDI-consent',
        ''
      ]
      const filtered = [...this.files, ...newFiles].filter(f => acceptedMimeTypes.includes(f.type))
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
      const [error] = await postUploadPDF(file, this.variantName)

      if (error !== undefined) {
        this.setSnackbar({
          message: 'There was an error uploading the file',
          show: true
        })
        return
      }

      this.setSnackbar({
        message: 'File uploaded successfully',
        show: true
      })
    },

    createOrder () {
      console.log('vc.files: ', this.files)
      if (this.files.length === 0) {
        this.setSnackbar({
          message: 'Please select a file to upload first',
          show: true
        })
        // alert('Please select a PDF to upload first')
        return
      }

      if (this.shouldAskForVariantName && (this.variantName === '' || this.variantName === null)) {
        this.setSnackbar({
          message: 'Please specify a variant name',
          show: true
        })
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
