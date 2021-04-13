<template>
  <div class="text-center">
    <v-dialog
      :value="open"
      max-width="1024"
      @click:outside="$emit('close')"
      @keydown.esc="$emit('close')"
    >
      <v-card>
        <v-card-title class="justify-space-between">
          <div class="secondary--text">
            Order #{{ order.id }} Audit Info
          </div>
          <v-btn
            icon
            @click="$emit('close')"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-divider />
        <v-card-text
          class="py-0 px-0"
        >
          <v-overlay
            :value="loading"
            absolute
          >
            <v-progress-circular
              indeterminate
              size="32"
            />
          </v-overlay>
          <AuditLogsTable :audits="audits" />
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import AuditLogsTable from '@/components/AuditLogsTable'

import { getAuditLogs } from '@/store/api_calls/utils'
import { flatMapAudits } from '@/utils/flatmap_audits'

export default {
  name: 'OrderAuditDialog',
  components: { AuditLogsTable },
  props: {
    open: {
      type: Boolean,
      required: true
    },
    order: {
      type: Object,
      required: true
    }
  },
  data: (vm) => ({
    audits: [],
    loading: false,
    showHidden: false,
  }),

  watch: {
    open () {
      if (!this.open) {
        this.audits = []
        return
      }

      this.fetchOrderAudits()
    }
  },
  methods: {
    async fetchOrderAudits () {
      this.loading = true
      const [error, data] = await getAuditLogs({ model_id: this.order.id, model_type: 'order' })
      this.loading = false

      if (error !== undefined) {
        return
      }

      const audits = data.data

      this.audits = flatMapAudits(audits.order, 'order')
        .concat(
          audits.order_line_items.map(item => {
            return flatMapAudits(item.audits, `order line item #${item.id}`)
          }).filter(item => item.length > 0).flat()
        )
        .concat(
          audits.order_address_events.map(item => {
            return flatMapAudits(item.audits, `order address event #${item.id}`)
          }).filter(item => item.length > 0).flat()
        )
    },
  }
}
</script>
<style lang="scss" scoped>
</style>
