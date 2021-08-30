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
            Upload Request(s)
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
                  v-model="company_id"
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
                <UploadOrdersFileFields
                  accept=".pdf,.xlsx,.csv,.edi"
                  :files="files"
                  @change="handleChange"
                  @clear="handleClear"
                />
                <div
                  v-if="shouldAskForVariantName"
                  class="mt-8"
                >
                  <v-autocomplete
                    v-model="variantName"
                    :items="variants"
                    item-value="abbyy_variant_name"
                    item-text="description"
                    label="Mapping Template Variant"
                    outlined
                    clearable
                    dense
                  />
                </div>
                <div
                  v-if="files.length > 0"
                  class="mt-4"
                >
                  <h2 class="submitted__title">
                    Documents to submit
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
            @click="handleClose"
          >
            cancel
          </v-btn>
          <v-btn
            color="primary"
            @click="createRequests"
          >
            Add to Requests Queue
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import UploadOrdersFileFields from './UploadOrdersFileFields'

import { postUploadRequestFile } from '@/store/api_calls/requests'
import { getVariants } from '@/store/api_calls/rules_editor'
import utils, { actionTypes as utilsActionTypes } from '@/store/modules/utils'
import auth from '@/store/modules/auth'
import { mapActions, mapState } from 'vuex'

import permissions from '@/mixins/permissions'
import allCompanies from '@/mixins/all_companies'

import { getVariantTypeFromFile, isPdf } from '@/utils/files_uploads'
import uniq from 'lodash/uniq'
import uniqBy from 'lodash/uniqBy'

export default {
  name: 'UploadOrdersDialog',
  components: { UploadOrdersFileFields },
  mixins: [permissions, allCompanies],
  props: {
    open: {
      type: Boolean,
      required: true
    },
    maxFiles: {
      type: Number,
      required: false,
      default: 20
    }
  },
  data: (vm) => ({
    files: [],
    variantName: null,
    variants: [],
    company_id: null
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
    company_id () { this.fetchVariantsForFiles() },
    files () { this.fetchVariantsForFiles() },
  },
  created () {
    if (this.canViewOtherCompanies()) {
      this.fetchCompanies()
    }
  },
  methods: {
    ...mapActions(utils.moduleName, [utilsActionTypes.setSnackbar]),

    handleClose () {
      this.files = []
      this.variantName = ''
      this.variants = []
      this.company_id = null
      this.$emit('close')
    },

    removeFile (file) {
      this.files = this.files.filter(f => f.name !== file.name)
    },

    handleClear () {
      this.files = []
    },

    handleChange (newFiles) {
      // const acceptedMimeTypes = [
      //   'application/pdf',
      //   'text/csv',
      //   'text/plain',
      //   'application/wps-office.xlsx',
      //   'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      //   'application/EDI-X12',
      //   'application/EDIFACT',
      //   'application/EDI-consent',
      //   'application/vnd.ms-excel',
      //   ''
      // ]
      // const filtered = [...this.files, ...newFiles].filter(f => acceptedMimeTypes.includes(f.type))
      [...this.files, ...newFiles].forEach(f => console.log(f.type))
      const finalFiles = [...this.files, ...newFiles]
      if (finalFiles.length > this.maxFiles) {
        this.setSnackbar({ message: 'Up to 20 files are allowed for upload' })
        this.files = []
        return
      }
      this.files = uniqBy(finalFiles, 'name')
    },

    async createRequests () {
      if (this.files.length === 0) {
        this.setSnackbar({ message: 'Please select a file to upload' })
        return
      }

      if (this.shouldAskForVariantName && (this.variantName === '' || this.variantName === null || this.variantName === undefined)) {
        this.setSnackbar({ message: 'Please specify a variant name' })
        return
      }

      if (this.canViewOtherCompanies() && this.company_id === null) {
        this.setSnackbar({ message: 'Please select a company' })
        return
      }

      let failed = false
      const requestsList = []
      for (let index = 0; index < this.files.length; index++) {
        const [error, data] = await this.uploadFile(this.files[index])
        if (error !== undefined) {
          failed = true
        } else {
          requestsList.push(data)
        }
      }

      this.setSnackbar({
        message: failed ? 'There was an error uploading the file(s)' : 'File(s) uploaded successfully',
      })
      this.variantName = null
      this.files = []
      this.$emit('uploaded', requestsList)
    },

    async uploadFile (file) {
      const params = { variant_name: this.variantName }
      if (this.canViewOtherCompanies()) {
        params.company_id = this.company_id
      }
      const [error, data] = await postUploadRequestFile(file, params)

      return [error, data]
    },

    async fetchVariantsForFiles () {
      const types = []
      const companyId = this.canViewOtherCompanies() ? this.company_id : this.currentUser.t_company_id

      this.files.forEach(file => {
        types.push(getVariantTypeFromFile(file))
      })

      if (!companyId || types.length === 0 || uniq(types).join('') === 'ocr') {
        return
      }

      const [, data] = await getVariants({
        'filter[company_id]': companyId,
        'filter[variant_type]': uniq(types).join(','),
        sort: 'description'
      })
      this.variants = data
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
