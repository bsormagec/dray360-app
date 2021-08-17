import events from '@/enums/events'
import permissions from '@/mixins/permissions'

export default {
  mixins: [permissions],

  computed: {
    channel () {
      let channel = 'request-status-updated'

      if (!this.canViewOtherCompanies()) {
        channel += `-company${this.currentUser.t_company_id}`
      }

      return channel
    }
  },

  methods: {
    listenToRequestStatusUpdates (handler) {
      this.$echo.private(this.channel).listen(events.requestStatusUpdated, handler)
    },

    leaveRequestStatusUpdatesChannel () {
      this.$echo.leave(this.channel)
    }
  }
}
