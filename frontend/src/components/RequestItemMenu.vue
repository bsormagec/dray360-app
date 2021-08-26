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
          v-if="active && !supervise && hasPermission('object-locks-edit') && !userOwnsLock(request.lock)"
          @click="handleClaimLock"
          v-text="'Take edit-lock'"
        />
        <v-list-item
          v-if="userOwnsLock(request.lock)"
          @click="handleReleaseLock"
          v-text="'Release edit-lock'"
        />
        <v-list-item
          v-if="!isPtImageUpload && canDownloadSourceFile"
          @click="downloadSourceFile"
          v-text="'Download source file'"
        />
        <v-list-item
          :disabled="!hasPermission('ocr-requests-remove') || isLocked"
          @click="deleteRequest"
          v-text="'Delete request'"
        />
        <v-list-item
          :disabled="!hasPermission('ocr-requests-edit') || isLocked"
          @click="toggleDoneUndone"
          v-text="`Mark as ${doneText}`"
        />
        <v-list-item
          v-if="!isPtImageUpload"
          :disabled="!hasPermission('ocr-request-statuses-create') || isLocked"
          @click="reprocessRequest"
          v-text="'Reprocess request'"
        />
        <v-list-item
          v-if="request.is_ocr_file && !isPtImageUpload"
          :disabled="!hasPermission('ocr-request-statuses-create') || isLocked"
          @click="reimportAbbyy"
          v-text="'Reimport from Abbyy'"
        />
        <v-list-item
          v-if="hasPermission('tms-submit') && request.orders_count > 0"
          @click="sendRequestOrdersToTms"
          v-text="'Send orders to TMS'"
        />
        <v-list-item
          v-if="request.has_email && !isPtImageUpload"
          @click="openEmailDialog = true"
          v-text="'Show email details'"
        />
        <v-list-item
          @click="openStatusHistoryDialog = true"
          v-text="'Show request history'"
        />
        <v-list-item
          v-if="hasPermission('feedbacks-create')"
          @click="openOrderCommentDialog"
          v-text="'Add a request comment'"
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
import { mapActions, mapState } from 'vuex'
import permissions from '@/mixins/permissions'
import locks from '@/mixins/locks'
import utils, { actionTypes as utilsActionTypes } from '@/store/modules/utils'
import requestList from '@/store/modules/requests-list'
import { downloadFile } from '@/utils/download_file'
import {
  deleteRequest,
  reprocessOcrRequest,
  sendRequestOrdersToTms,
  changeRequestDoneStatus,
  reimportOcrRequestAbbyy,
  getSourceFileDownloadURL,
} from '@/store/api_calls/requests'
import RequestStatusHistoryDialog from './RequestStatusHistoryDialog'
import RequestEmailDialog from './RequestEmailDialog'
import { objectLocks, commentableTypes, displayStatuses } from '@/enums/app_objects_types'
import events from '@/enums/events'
import { isPtImageUpload } from '@/utils/status_helpers'

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
    ...mapState(requestList.moduleName, {
      supervise: state => state.supervise
    }),
    doneText () {
      return this.request.done_at === null ? 'complete' : 'not complete'
    },
    isLocked () {
      return this.request.is_locked || this.supervise
    },
    isPtImageUpload () {
      return isPtImageUpload(this.request.latest_ocr_request_status?.status)
    },
    canDownloadSourceFile () {
      return this.request.display_status !== displayStatuses.rejected && this.request.has_upload_requested
    }
  },
  methods: {
    ...mapActions(utils.moduleName, [utilsActionTypes.setSnackbar, utilsActionTypes.setConfirmationDialog]),

    handleClipboardSuccess () {
      this.setSnackbar({ message: 'Request ID coppied to clipboard.' })
    },

    async deleteRequest () {
      this.loading = true
      await this.setConfirmationDialog({
        title: 'Are you sure you want to delete this request?',
        onConfirm: async () => {
          this.loading = true
          const [error] = await deleteRequest(this.request.request_id)
          let message = ''
          if (!error) {
            message = 'Request deleted'
            this.loading = false
            this.$emit('request-deleted')
          } else {
            message = 'Error trying to delete the request'
          }
          await this.setSnackbar({ message })
        },
        onCancel: () => {
          this.loading = false
        }
      })
    },

    async handleClaimLock () {
      this.loading = true
      await this.setConfirmationDialog({
        title: 'Are you sure you want to take the request edit-lock?',
        noWrap: true,
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
      await this.setConfirmationDialog({
        title: 'Are you sure you want to release the request lock?',
        onConfirm: async () => {
          this.releaseLockRequest({ requestId: this.request.request_id, updateList: true, })

          this.$root.$emit(events.lockReleased, this.request)
          this.loading = false
        },
        onCancel: () => {
          this.loading = false
        }
      })
    },

    async downloadSourceFile () {
      const [error, data] = await getSourceFileDownloadURL(this.request.request_id)

      if (error === undefined) {
        downloadFile(data.data)
      } else {
        await this.setSnackbar({ message: error.response?.data?.message })
      }
    },

    async reprocessRequest () {
      this.setConfirmationDialog({
        title: 'Are you sure you want to reprocess the request?',
        onConfirm: async () => {
          this.loading = true

          const [error] = await reprocessOcrRequest(this.request.request_id)

          if (error !== undefined) {
            this.loading = false
            this.setSnackbar({ message: 'There was an error trying to send the message to reprocess' })
            return
          }

          this.setSnackbar({ message: 'Request sent for reprocessing' })
          this.loading = false
          this.$emit('reload-request')
        }
      })
    },

    async reimportAbbyy () {
      this.setConfirmationDialog({
        title: 'Are you sure you want to reimport the request from Abbyy?',
        onConfirm: async () => {
          this.loading = true

          const [error] = await reimportOcrRequestAbbyy(this.request.request_id)

          if (error !== undefined) {
            this.loading = false
            this.setSnackbar({ message: 'There was an error trying to send the message to reimport' })
            return
          }

          this.setSnackbar({ message: 'Request sent for reimporting' })
          this.loading = false
          this.$emit('reload-request')
        }
      })
    },

    async sendRequestOrdersToTms () {
      this.setConfirmationDialog({
        title: 'Send orders to TMS',
        text: 'Are you sure you want to send all the request orders to the TMS?',
        onConfirm: async () => {
          this.loading = true

          const [error, data] = await sendRequestOrdersToTms(this.request.request_id)

          if (error !== undefined) {
            this.loading = false
            this.setSnackbar({ message: 'There was an error trying to send the orders to the tms' })
            return
          }

          const { data: counters } = data
          this.setSnackbar({
            message: `${counters.sent} order(s) queued to be sent. \n` +
              ` ${counters.not_sent} order(s) not queued. \n` +
              ` ${counters.failed} order(s) failed to queue. \n`,
            multiline: true,
            timeout: -1,
          })
          console.log(counters.messages)

          this.loading = false
          this.$emit('reload-request')
        }
      })
    },

    async toggleDoneUndone () {
      this.loading = true

      const [error] = await changeRequestDoneStatus(this.request.request_id, { done: this.request.done_at === null })

      if (error !== undefined) {
        this.loading = false
        this.setSnackbar({ message: `There was an error trying to mark the request as ${this.doneText}` })
        return
      }

      this.setSnackbar({ message: `Request marked as ${this.doneText} successfully` })
      this.loading = false
      this.$emit('request-deleted')
    },

    openOrderCommentDialog () {
      this.$root.$emit(events.openOrderCommentDialog, {
        commentableType: commentableTypes.request,
        commentableId: this.request.request_id,
        label: `Request #${this.request.id} Feedback`
      })
    },
  }
}
</script>
<style lang="scss" scoped>
</style>
