import { diffForHumans } from '@/utils/dates'
import { cleanStrForId } from '@/utils/clean_str_for_id'
import { getStatusHistory } from '@/store/api_calls/utils'

export default {
  data: (vm) => ({
    statusHistory: [],
    loading: false,
    useSystemStatus: vm.hasPermission('system-status-filter')
  }),

  methods: {
    async fetchStatusHistory (filters) {
      this.loading = true
      const [error, data] = await getStatusHistory({ ...filters, system_status: this.useSystemStatus })
      this.loading = false

      if (error !== undefined) {
        return
      }
      this.statusHistory = data.data
    },

    getStatusClass (status) {
      return cleanStrForId(status)
    },

    formatJson (status) {
      if (status === undefined) {
        return {}
      }

      // eslint-disable-next-line camelcase
      const { status_metadata, start_date, company_id } = status

      return { start_date, company_id, status_metadata }
    },

    updateStatusHistory (latestStatus) {
      const lastStatusHistoryItem = this.statusHistory[this.statusHistory.length - 1]
      lastStatusHistoryItem.end_date = latestStatus.status_date
      lastStatusHistoryItem.diff_for_humans = diffForHumans(
        lastStatusHistoryItem.start_date,
        lastStatusHistoryItem.end_date
      )

      if (lastStatusHistoryItem.status !== latestStatus.status) {
        this.statusHistory.push({
          company_id: latestStatus.company_id,
          diff_for_humans: null,
          display_status: latestStatus.display_status,
          end_date: null,
          start_date: lastStatusHistoryItem.end_date,
          status: this.useSystemStatus ? latestStatus.status : latestStatus.display_status,
          status_metadata: this.useSystemStatus ? latestStatus.status_metadata : null,
        })
      }
    },
  }
}
