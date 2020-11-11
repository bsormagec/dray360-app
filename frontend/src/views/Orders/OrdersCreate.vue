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
      <v-autocomplete
        v-model="variantName"
        :items="variants"
        item-value="abbyy_variant_name"
        item-text="description"
        label="Variant name"
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
import { getVariantList } from '@/store/api_calls/rules_editor'
import utils, { type } from '@/store/modules/utils'
import auth from '@/store/modules/auth'
import { mapActions, mapState } from 'vuex'

import { getVariantTypeFromFile, isPdf } from '@/utils/files_uploads'
import uniq from 'lodash/uniq'
import uniqBy from 'lodash/uniqBy'

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
    variantName: null,
    variants: []
  }),

  computed: {
    ...mapState(auth.moduleName, { currentUser: state => state.currentUser }),
    shouldAskForVariantName () {
      for (let index = 0; index < this.files.length; index++) {
        if (!isPdf(this.files[index])) {
          return true
        }
      }
      return false
    }
  },

  watch: {
    files: async function (files) {
      const types = []

      files.forEach(file => {
        types.push(getVariantTypeFromFile(file))
      })

      if (uniq(types).join('') === 'ocr') return

      const [error, data] = await getVariantList({
        'filter[company_id]': this.currentUser.t_company_id,
        'filter[variant_type]': uniq(types).join(','),
        sort: 'description'
      })
      this.variants = data
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
      this.files = uniqBy(filtered, 'name')
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
      if (this.files.length === 0) {
        this.setSnackbar({
          message: 'Please select a file to upload first',
          show: true
        })
        return
      }

      if (this.shouldAskForVariantName && (this.variantName === '' || this.variantName === null || this.variantName === undefined)) {
        this.setSnackbar({
          message: 'Please specify a variant name',
          show: true
        })
        return
      }

      this.files.forEach(file => this.uploadFile(file))
      this.variantName = null
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

  @include media("med") {
    min-width: 25%;
    max-width: 25%;
    box-shadow: map-get($properties, inset-shadow-left);
    border-left: rem(1) solid map-get($colors, grey-2);
    padding: rem(52) rem(16);
    padding-bottom: rem(30);
  }

  @include media("lg") {
    padding: rem(52) rem(36);
  }
}

.create__close {
  @include media("med") {
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
