<template>
  <div class="text-center">
    <v-dialog
      :value="open"
      max-width="400"
      @click:outside="$emit('close')"
      @keydown.esc="$emit('close')"
    >
      <v-card>
        <v-card-title class="justify-space-between">
          <div class="secondary--text">
            Request #{{ request.request_id.substring(0,8).toUpperCase() }} email
          </div>
          <v-btn
            icon
            @click="$emit('close')"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-divider />
        <v-card-text class="mt-4 pb-0">
          <v-overlay
            :value="loading"
            absolute
          >
            <v-progress-circular
              indeterminate
              size="32"
            />
          </v-overlay>
          <div class="email__details">
            <div class="subtitle-2 email__details-title">
              Submitted:
            </div>
            <div class="body-2">
              {{ formatDate(request.created_at, { timeZone: true }) }}
            </div>
          </div>
          <div class="email__details">
            <div class="subtitle-2 email__details-title ">
              From:
            </div>
            <div class="body-2">
              {{ emailFromAddress }}
            </div>
          </div>
          <div class="email__details">
            <div class="subtitle-2 email__details-title">
              Subject:
            </div>
            <div class="body-2">
              {{ emailSubject }}
            </div>
          </div>
          <div
            v-if="attachmentFileNames.length > 0"
            class="email__details"
          >
            <div class="subtitle-2 email__details-title">
              Attachment file names:
            </div>
            <div
              class="body-2"
            >
              {{ attachmentFileNames.join(', ') }}
            </div>
          </div>
          <div
            v-if="rejectionReason"
            class="email__details"
          >
            <div class="subtitle-2 email__details-title ">
              Rejection reason:
            </div>
            <div class="body-2">
              {{ rejectionReason }}
            </div>
          </div>
        </v-card-text>
        <v-card-actions class="justify-end">
          <v-btn
            :loading="loading"
            :disabled="loading"
            color="primary"
            text
            @click="downloadSourceEmail"
          >
            Download EML file
            <v-icon
              right
              dark
            >
              mdi-cloud-download
            </v-icon>
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import get from 'lodash/get'
import { mapActions } from 'vuex'
import { formatDate } from '@/utils/dates'
import utils, { type } from '@/store/modules/utils'
import { downloadFile } from '@/utils/download_file'
import { getEmailDetails, getSourceEmailDownloadURL } from '@/store/api_calls/requests'

export default {
  name: 'RequestEmailDialog',
  props: {
    open: {
      type: Boolean,
      required: true
    },
    request: {
      type: Object,
      required: true
    }
  },
  data: (vm) => ({
    statusMetadata: null,
    loading: false
  }),

  computed: {
    emailSubject () {
      return get(this.statusMetadata, 'source_summary.source_email_subject', 'Not available')
    },
    emailFromAddress () {
      return get(this.statusMetadata, 'source_summary.source_email_from_address', 'Not available')
    },
    attachmentFileNames () {
      return get(this.statusMetadata, 'source_summary.source_email_attachment_filenames', [])
    },
    rejectionReason () {
      return get(this.statusMetadata, 'exception_message', null)
    }
  },

  watch: {
    open () {
      if (this.statusMetadata !== null) {
        return
      }

      this.fetchEmailDetails()
    }
  },
  methods: {
    ...mapActions(utils.moduleName, { setSnackbar: type.setSnackbar }),
    formatDate,
    async fetchEmailDetails () {
      this.loading = true
      const [error, data] = await getEmailDetails(this.request.request_id)
      this.loading = false

      if (error !== undefined) {
        return
      }
      this.statusMetadata = data.data
    },
    async downloadSourceEmail () {
      const [error, data] = await getSourceEmailDownloadURL(this.request.request_id)

      if (error === undefined) {
        downloadFile(data.data)
      } else {
        await this.setSnackbar({ show: true, message: error })
      }
    }
  }
}
</script>
<style lang="scss" scoped>
.email__details {
  display: flex;
  flex-direction: column;
  margin-bottom:rem(8);

  div {
    color: var(--v-black-base) !important;
  }

  .email__details-title {
    margin-right: rem(8);
    font-weight: 700;
  }
}
</style>
