<template>
  <v-dialog
    :value="open"
    width="510"
    retain-focus
    @click:outside="handleClose"
    @keydown.esc="handleClose"
  >
    <v-card>
      <v-card-title>
        {{ label }}
        <v-spacer />
        <v-btn
          icon
          :disabled="loading"
          @click="handleClose"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <v-card-text v-if="loading">
        Loading...
        <v-progress-linear
          indeterminate
          color="primary"
          class="mb-0"
        />
      </v-card-text>
      <v-card-text v-else>
        <template v-if="hasPermission('feedbacks-view')">
          <template v-for="item in savedComments">
            <v-card-text
              :key="item.id"
              class="px-0 py-1"
            >
              <strong>By {{ item.user.name }}</strong>
              <br>
              <span class="caption">{{ formatDate(item.created_at) }}</span>
              <br>
              {{ item.comment }}
            </v-card-text>
            <v-divider :key="`divider-${item.id}`" />
          </template>
        </template>
        <v-textarea
          v-model="comment"
          :class="{'mt-4': hasPermission('feedbacks-view') && savedComments.length}"
          outlined
          name="order-comments-input"
          label="Comments"
          hide-details
        />
      </v-card-text>

      <v-divider />

      <v-card-actions>
        <v-btn
          text
          @click="handleClose"
        >
          Cancel
        </v-btn>
        <v-spacer />
        <v-btn
          color="primary"
          :disabled="loading"
          @click="uploadComment"
        >
          Send Feedback
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import { mapActions } from 'vuex'
import permissions from '@/mixins/permissions'
import utils, { actionTypes } from '@/store/modules/utils'
import { formatDate } from '@/utils/dates'
import { addFeedback, getFeedback } from '@/store/api_calls/feedbacks'
import events from '@/enums/events'

export default {
  name: 'OrderCommentDialog',

  mixins: [permissions],

  data: () => ({
    open: false,
    loading: false,
    savedComments: [],
    comment: '',
    commentableId: null,
    commentableType: null,
    label: '',
  }),

  watch: {
    open () {
      if (!this.open) {
        this.savedComments = []
        this.comment = ''
        return
      }
      if (this.hasPermission('feedbacks-view')) this.fetchFeedback()
    }
  },

  created () {
    this.$root.$on(events.openOrderCommentDialog, this.handleOpen)
  },

  methods: {
    ...mapActions(utils.moduleName, [actionTypes.setSnackbar]),

    formatDate,

    handleOpen ({ commentableId = null, commentableType = null, label = null } = {}) {
      this.commentableId = commentableId
      this.commentableType = commentableType
      this.label = label
      this.open = true
    },

    handleClose () {
      this.open = false
    },

    async uploadComment () {
      this.loading = true
      const [error] = await addFeedback({
        commentable_type: this.commentableType,
        commentable_id: this.commentableId,
        comment: this.comment,
      })
      this.loading = false
      if (error !== undefined) {
        this.setSnackbar({ message: 'Something went wrong, please try again' })
        return
      }
      this.setSnackbar({ message: 'Comment sent successfully' })
      this.open = false
    },

    async fetchFeedback () {
      this.loading = true
      const [error, data] = await getFeedback({
        'filter[commentable_type]': this.commentableType,
        'filter[commentable_id]': this.commentableId,
      })
      this.loading = false
      if (error !== undefined) {
        return
      }
      this.savedComments = data.data
    },
  },
}
</script>
