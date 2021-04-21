<template>
  <div class="text-center">
    <v-dialog
      :value="open"
      width="500"
      @click:outside="handleClose"
      @keydown.esc="handleClose"
    >
      <v-card>
        <v-card-title>
          <h6 class="secondary--text">
            Upload Profit Tools image
          </h6>
        </v-card-title>
        <v-divider />

        <v-card-text class="mb-1 pb-0">
          <v-container class="mb-0 pb-0">
            <v-row
              v-if="canViewOtherCompanies()"
            >
              <v-col class="py-0">
                <v-autocomplete
                  v-model="companyId"
                  :items="companies"
                  label="Select Company"
                  item-value="id"
                  item-text="name"
                  dense
                  outlined
                />
              </v-col>
            </v-row>
            <v-row>
              <v-col class="py-0">
                <v-autocomplete
                  v-model="ptImageType"
                  :items="ptImageTypes"
                  label="Image Type"
                  item-value="id"
                  item-text="item_display_name"
                  dense
                  outlined
                  @update:search-input="search => ptImageTypeSearch = search"
                >
                  <template
                    v-slot:no-data
                    :companyId="companyId"
                    :ptImageTypeSearch="ptImageTypeSearch"
                  >
                    <v-list-item>
                      <v-list-item-title v-if="companyId === null || ptImageTypeSearch === null || ptImageTypeSearch === ''">
                        No data available
                      </v-list-item-title>
                      <v-list-item-title
                        v-else
                        style="cursor:pointer"
                        @click="addImageTypeItem"
                      >
                        Add <strong>{{ ptImageTypeSearch }}</strong> image type
                      </v-list-item-title>
                    </v-list-item>
                  </template>
                </v-autocomplete>
              </v-col>
            </v-row>
            <v-row>
              <v-col class="py-0">
                <v-text-field
                  v-model="tmsShipmentId"
                  label="TMS Shipment ID"
                  :disabled="lockTmsShipmentId"
                  dense
                  outlined
                />
              </v-col>
            </v-row>
            <v-row>
              <v-col class="py-0">
                <div
                  class="d-flex flex-column"
                  style="position: relative;"
                >
                  <div class="mb-4">
                    <v-file-input
                      dense
                      outlined
                      hide-details
                      label="Select file"
                      :value="files"
                      :accept="accept"
                      @change="handleChange"
                      @click:clear="handleClear"
                    />

                    <span class="input__legend">
                      <strong>Supported file types:</strong> {{ accept.toUpperCase() }}
                    </span>
                  </div>
                </div>
                <div v-if="files.length > 0">
                  <h2 class="submitted__title">
                    Image to submit
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
                      @click="removeFile(file)"
                    >
                      <v-icon color="red">
                        mdi-delete
                      </v-icon>
                    </v-btn>
                  </div>
                </div>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>

        <v-divider />

        <v-card-actions class="d-flex justify-space-between px-10 py-4">
          <v-btn
            text
            color="primary"
            :loading="loading"
            @click="handleClose"
          >
            cancel
          </v-btn>
          <v-btn
            color="primary"
            :loading="loading"
            @click="uploadImages"
          >
            Upload image
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>

import { postUploadPtImageFile } from '@/store/api_calls/utils'
import { getDictionaryItems, createDictionaryItem } from '@/store/api_calls/dictionary_items'
import utils, { type } from '@/store/modules/utils'
import auth from '@/store/modules/auth'
import { mapActions, mapState } from 'vuex'
import { getCompanies } from '@/store/api_calls/companies'
import permissions from '@/mixins/permissions'

import events from '@/enums/events'
import { dictionaryItemsTypes } from '@/enums/app_objects_types'

export default {
  name: 'PtImageUploadDialog',
  mixins: [permissions],
  data: () => ({
    open: false,
    loading: false,
    lockTmsShipmentId: false,
    accept: '.jpg,.jpeg',
    files: [],
    tmsShipmentId: null,
    orderId: null,
    companyId: null,
    ptImageType: null,
    ptImageTypeSearch: null,
    ptImageTypes: [],
    companies: [],
  }),
  computed: {
    ...mapState(auth.moduleName, { currentUser: state => state.currentUser }),
  },

  watch: {
    async companyId () {
      if (this.companyId === null || this.companyId === undefined) {
        return
      }

      await this.getPTImageTypes()
    }
  },

  created () {
    this.$root.$on(events.openPtFileUploadDialog, this.handleOpen)
  },

  methods: {
    ...mapActions(utils.moduleName, { setSnackbar: type.setSnackbar }),

    async addImageTypeItem () {
      if (!this.companyId || !this.ptImageTypeSearch) {
        return
      }

      this.loading = true
      const [error, data] = await createDictionaryItem({
        item_display_name: this.ptImageTypeSearch,
        item_key: this.ptImageTypeSearch,
        t_company_id: this.companyId,
        item_type: dictionaryItemsTypes.ptImageType,
      })
      this.loading = false

      if (error !== undefined) {
        return
      }

      this.ptImageTypes.push(data)
      this.ptImageType = data.id
    },

    async handleOpen ({ orderId = null, tmsShipmentId = null, companyId = null } = {}) {
      this.orderId = orderId
      this.tmsShipmentId = tmsShipmentId
      this.lockTmsShipmentId = tmsShipmentId !== null

      if (this.canViewOtherCompanies() && this.companies.length === 0) {
        await this.getCompanies()
      } else if (!this.canViewOtherCompanies()) {
        this.companyId = this.currentUser.t_company_id
      }

      if (companyId !== undefined) {
        this.companyId = companyId
      }

      this.open = true
    },
    handleClose () {
      this.files = []
      this.tmsShipmentId = null
      this.orderId = null
      this.companyId = null
      this.ptImageType = null
      this.ptImageTypes = []
      this.open = false
      this.lockTmsShipmentId = false
    },
    removeFile (file) {
      this.files = this.files.filter(f => f.name !== file.name)
    },
    handleClear () {
      this.files = []
    },
    handleChange (file) {
      if (!file || file.name === undefined || file.name === '' || file.name === null) {
        return
      }

      this.files = [file]
    },
    async uploadImages () {
      if (this.files.length === 0) {
        this.setSnackbar({
          message: 'Please select a file to upload',
          show: true
        })
        return
      }

      if (this.tmsShipmentId === null) {
        this.setSnackbar({
          message: 'Please specify a TMS Shipment ID',
          show: true
        })
        return
      }

      if (this.ptImageType === null || this.ptImageType === undefined) {
        this.setSnackbar({
          message: 'Please specify an image type',
          show: true
        })
        return
      }

      if (this.canViewOtherCompanies() && this.companyId === null) {
        this.setSnackbar({
          message: 'Please select a company',
          show: true
        })
        return
      }

      let error = false
      this.loading = true
      for (let index = 0; index < this.files.length; index++) {
        const success = await this.uploadFile(this.files[index])
        if (!success) {
          error = true
        }
      }
      this.loading = false

      this.setSnackbar({
        message: error ? 'There was an error uploading the file' : 'File uploaded successfully',
        show: true
      })
      this.variantName = null
      this.files = []
      this.$emit('uploaded')
    },

    async uploadFile (file) {
      const params = {
        company_id: this.companyId,
        order_id: this.orderId || null,
        request_id: this.request_id || null,
        tms_shipment_id: this.tmsShipmentId,
        pt_image_type: this.ptImageType,
      }

      const [error] = await postUploadPtImageFile(file, params)

      return error === undefined
    },

    async getCompanies () {
      const [error, data] = await getCompanies()
      if (error) return
      this.companies = data.data
    },

    async getPTImageTypes () {
      const [error, data] = await getDictionaryItems({
        'filter[item_type]': dictionaryItemsTypes.ptImageType,
        'filter[company_id]': this.companyId,
      })
      if (error) return
      this.ptImageTypes = data.data
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
  margin-bottom: rem(12);

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
