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
    listenToRequestStatusUpdates (handler, extraChannelInformation = '') {
      this.$echo.private(`${this.channel}${extraChannelInformation}`).listen(events.requestStatusUpdated, handler)
    },

    leaveRequestStatusUpdatesChannel (extraChannelInformation = '') {
      this.$echo.leave(`${this.channel}${extraChannelInformation}`)
    }
  }
}
