<template>
  <div>
    <v-menu
      offset-y
      offset-x
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          icon
          color="primary"
          small
          v-bind="attrs"
          :loading="loading"
          :disabled="disabled"
          v-on="on"
        >
          <v-icon>
            mdi-dots-horizontal
          </v-icon>
        </v-btn>
      </template>
      <v-list>
        <v-list-item
          v-if="isSuperadmin()"
          v-clipboard="request.request_id"
          @click="handleClipboardSuccess"
          v-text="'Copy request ID'"
        />
        <v-list-item
          v-if="request.has_email"
          @click="downloadSourceEmail(request.request_id)"
          v-text="'Download source email'"
        />
        <v-list-item
          @click="downloadSourceFile(request.request_id)"
          v-text="'Download source file'"
        />
        <v-list-item
          :disabled="!hasPermission('ocr-requests-remove')"
          @click="deleteRequest(request.request_id)"
          v-text="'Delete request'"
        />
        <v-list-item
          :disabled="!hasPermission('ocr-requests-edit')"
          @click="toggleDoneUndone(request)"
          v-text="`Mark as ${doneText}`"
        />
        <v-list-item
          :disabled="!hasPermission('ocr-request-statuses-create')"
          @click="reprocessRequest(request.request_id)"
          v-text="'Reprocess request'"
        />
        <v-list-item
          @click="openStatusHistoryDialog = true"
          v-text="'Show request history'"
        />
      </v-list>
    </v-menu>
    <RequestStatusHistoryDialog
      :request="request"
      :open="openStatusHistoryDialog"
      @close="openStatusHistoryDialog = false"
    />
  </div>
</template>
<script>
import { mapActions } from 'vuex'
import permissions from '@/mixins/permissions'
import utils, { type } from '@/store/modules/utils'
import { downloadFile } from '@/utils/download_file'
import {
  deleteRequest,
  getSourceFileDownloadURL,
  reprocessOcrRequest,
  changeRequestDoneStatus,
  getSourceEmailDownloadURL
} from '@/store/api_calls/requests'
import RequestStatusHistoryDialog from './RequestStatusHistoryDialog'

export default {
  name: 'RequestItemMenu',
  components: { RequestStatusHistoryDialog },
  mixins: [permissions],
  props: {
    request: {
      type: Object,
      required: true,
      defautl: () => { }
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false
    }
  },
  data () {
    return {
      loading: false,
      openStatusHistoryDialog: false
    }
  },
  computed: {
    doneText () {
      return this.request.done_at === null ? 'complete' : 'not complete'
    }
  },
  methods: {
    ...mapActions(utils.moduleName, {
      setSnackbar: type.setSnackbar,
      setConfirmDialog: type.setConfirmationDialog
    }),
    handleClipboardSuccess () {
      this.setSnackbar({ show: true, message: 'Request ID coppied to clipboard.' })
    },
    async deleteRequest (requestId) {
      this.loading = true
      await this.setConfirmDialog({
        title: 'Are you sure you want to delete this request?',
        onConfirm: async () => {
          this.loading = true
          const [error] = await deleteRequest(requestId)
          let message = ''
          if (!error) {
            message = 'Request deleted'
            this.loading = false
            this.$emit('request-deleted')
          } else {
            message = 'Error trying to delete the request'
          }
          await this.setSnackbar({ show: true, message })
        },
        onCancel: () => {
          this.loading = false
        }
      })
    },

    async downloadSourceFile (requestId) {
      const [error, data] = await getSourceFileDownloadURL(requestId)

      if (error === undefined) {
        downloadFile(data.data)
      } else {
        await this.setSnackbar({ show: true, message: error })
      }
    },

    async downloadSourceEmail (requestId) {
      const [error, data] = await getSourceEmailDownloadURL(requestId)

      if (error === undefined) {
        downloadFile(data.data)
      } else {
        await this.setSnackbar({ show: true, message: error })
      }
    },

    async reprocessRequest (requestId) {
      this.setConfirmDialog({
        title: 'Are you sure you want to reprocess the request?',
        onConfirm: async () => {
          this.loading = true

          const [error] = await reprocessOcrRequest(requestId)

          if (error !== undefined) {
            this.loading = false
            this.setSnackbar({ show: true, message: 'There was an error trying to send the message to reprocess' })
            return
          }

          this.setSnackbar({ show: true, message: 'Request sent for reprocessing' })
          this.loading = false
          this.$emit('request-deleted')
        }
      })
    },
    async toggleDoneUndone (request) {
      this.loading = true

      const [error] = await changeRequestDoneStatus(request.request_id, { done: request.done_at === null })

      if (error !== undefined) {
        this.loading = false
        this.setSnackbar({ show: true, message: `There was an error trying to mark the request as ${this.doneText}` })
        return
      }

      this.setSnackbar({ show: true, message: `Request marked as ${this.doneText} successfully` })
      this.loading = false
      this.$emit('request-deleted')
    }
  }
}
</script>
<style lang="scss" scoped>
</style>
