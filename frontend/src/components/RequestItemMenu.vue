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
          v-if="active && isLocked && hasPermission('object-locks-edit')"
          @click="handleClaimLock"
          v-text="'Claim lock'"
        />
        <v-list-item
          v-if="userOwnsLock(request.lock)"
          @click="handleReleaseLock"
          v-text="'Release lock'"
        />
        <v-list-item
          @click="downloadSourceFile(request.request_id)"
          v-text="'Download source file'"
        />
        <v-list-item
          :disabled="!hasPermission('ocr-requests-remove') || isLocked"
          @click="deleteRequest(request.request_id)"
          v-text="'Delete request'"
        />
        <v-list-item
          :disabled="!hasPermission('ocr-requests-edit') || isLocked"
          @click="toggleDoneUndone(request)"
          v-text="`Mark as ${doneText}`"
        />
        <v-list-item
          :disabled="!hasPermission('ocr-request-statuses-create') || isLocked"
          @click="reprocessRequest(request.request_id)"
          v-text="'Reprocess request'"
        />
        <v-list-item
          v-if="request.has_email"
          @click="openEmailDialog = true"
          v-text="'Show email details'"
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
    <RequestEmailDialog
      v-if="request.has_email"
      :open="openEmailDialog"
      :request="request"
      @close="openEmailDialog = false"
    />
  </div>
</template>
<script>
import { mapActions } from 'vuex'
import permissions from '@/mixins/permissions'
import locks from '@/mixins/locks'
import utils, { type } from '@/store/modules/utils'
import { downloadFile } from '@/utils/download_file'
import { deleteRequest, getSourceFileDownloadURL, reprocessOcrRequest, changeRequestDoneStatus } from '@/store/api_calls/requests'
import RequestStatusHistoryDialog from './RequestStatusHistoryDialog'
import RequestEmailDialog from './RequestEmailDialog'
import { objectLocks } from '@/enums/app_objects_types'
import events from '@/enums/events'

export default {
  name: 'RequestItemMenu',
  components: { RequestStatusHistoryDialog, RequestEmailDialog },
  mixins: [permissions, locks],
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
    },
    active: {
      type: Boolean,
      required: false,
      default: false
    }
  },
  data () {
    return {
      loading: false,
      openStatusHistoryDialog: false,
      openEmailDialog: false
    }
  },
  computed: {
    doneText () {
      return this.request.done_at === null ? 'complete' : 'not complete'
    },
    isLocked () {
      return this.request.is_locked
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

    async handleClaimLock () {
      this.loading = true
      await this.setConfirmDialog({
        title: 'Are you sure you want to claim this request\'s lock?',
        onConfirm: async () => {
          this.attemptToLockRequest({
            requestId: this.request.request_id,
            lockType: objectLocks.lockTypes.claimLock,
            updateList: true,
            startRefresh: false,
          })

          this.$root.$emit(events.lockClaimed, this.request)
          this.loading = false
        },
        onCancel: () => {
          this.loading = false
        }
      })
    },

    async handleReleaseLock () {
      this.loading = true
      await this.setConfirmDialog({
        title: 'Are you sure you want to release this request\'s lock?',
        onConfirm: async () => {
          this.releaseLockRequest({ requestId: this.request.request_id })

          this.$root.$emit(events.lockReleased, this.request)
          this.loading = false
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
