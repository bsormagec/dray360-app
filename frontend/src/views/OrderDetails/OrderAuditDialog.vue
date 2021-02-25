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

          <v-simple-table
            dense
            fixed-header
            height="55vh"
          >
            <template v-slot:default>
              <thead>
                <tr>
                  <th class="text-left">
                    Object
                  </th>
                  <th class="text-left">
                    Attribute
                  </th>
                  <th class="text-left">
                    From
                  </th>
                  <th class="text-left">
                    To
                  </th>
                  <th class="text-left">
                    Date
                  </th>
                  <th class="text-left">
                    User
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(audit, index) in audits"
                  :key="index"
                >
                  <td class="caption black--text">
                    {{ audit.model_type }}
                  </td>
                  <td class="caption black--text">
                    <pre>{{ audit.attribute }}</pre>
                  </td>
                  <td class="wrap-overflow old">
                    <pre>{{ audit.old }}</pre>
                  </td>
                  <td class="wrap-overflow new">
                    <pre>{{ audit.new }}</pre>
                  </td>
                  <td
                    class="caption black--text"
                  >
                    {{ formatDate(audit.updated_at, true) }}
                  </td>
                  <td class="caption black--text">
                    {{ audit.user }}
                  </td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import { getAuditLogs } from '@/store/api_calls/utils'
import { formatDate } from '@/utils/dates'
import flatten from 'lodash/flatten'

export default {
  name: 'OrderAuditDialog',
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
    loading: false
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
    formatDate,
    async fetchOrderAudits () {
      this.loading = true
      const [error, data] = await getAuditLogs({ model_id: this.order.id, model_type: 'order' })
      this.loading = false

      if (error !== undefined) {
        return
      }

      const audits = data.data

      this.audits = this.flatMapAudits(audits.order, 'order')
        .concat(
          audits.order_line_items.map(item => {
            return this.flatMapAudits(item.audits, `order line item #${item.id}`)
          }).filter(item => item.length > 0).flat()
        )
        .concat(
          audits.order_address_events.map(item => {
            return this.flatMapAudits(item.audits, `order address event #${item.id}`)
          }).filter(item => item.length > 0).flat()
        )
    },
    flatMapAudits (audits, modelType) {
      const auditsArray = []
      for (const key in audits) {
        auditsArray.push(audits[key])
      }

      return flatten(auditsArray).map(audit => {
        audit.model_type = modelType
        return audit
      })
    }
  }
}
</script>
<style lang="scss" scoped>
.wrap-overflow {
  max-width: rem(160);

  &.old {
    background-color: var(--v-error-lighten4);
  }
  &.new {
    background-color: var(--v-success-lighten4);
  }

  pre {
    white-space: normal;
  }
}
</style>
