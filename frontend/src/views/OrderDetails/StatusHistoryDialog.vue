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
            Order #{{ order.id }} History
            <v-btn
              outlined
              dense
              small
              icon
              color="primary"
              class="refresh__button"
              :loading="loading"
              @click="fetchHistory"
            >
              <v-icon>mdi-refresh</v-icon>
            </v-btn>
          </div>
          <v-btn
            icon
            @click="$emit('close')"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-divider />
        <v-card-text class="mt-4">
          <v-overlay
            :value="loading"
            absolute
          >
            <v-progress-circular
              indeterminate
              size="32"
            />
          </v-overlay>
          <v-switch
            v-if="hasPermission('system-status-filter')"
            v-model="useSystemStatus"
            label="Show system status"
            hide-details
            :false-value="false"
            :true-value="true"
          />
          <div
            v-if="order.submitted_date"
            class="caption my-3"
          >
            Submitted {{ formatDate(order.submitted_date, { timeZone: true }) }} in <router-link :to="`/dashboard?selected=${order.request_id}`">
              Request #{{ order.request_id.substring(0,8).toUpperCase() }}
            </router-link>
            <br>
            {{ userWhoUploadedTheRequest ? `By ${userWhoUploadedTheRequest}` : '' }}
          </div>
          <div
            v-else
            class="caption mb-3"
          >
            Submitted in <router-link :to="`/dashboard?selected=${order.request_id}`">
              Request #{{ order.request_id.substring(0,8).toUpperCase() }}
            </router-link>
            <br>
            {{ userWhoUploadedTheRequest ? `By ${userWhoUploadedTheRequest}` : '' }}
          </div>
          <div v-if="hasPermission('system-status-filter')">
            <div class="caption mt-3">
              {{ `Company: ${historyLabels.companyName}` }}
            </div>
            <div class="caption">
              {{ `Variant: ${historyLabels.variantName} (${historyLabels.variantId})` }}
            </div>
            <div class="caption mb-3">
              {{ `Abbyy Verifier: ${historyLabels.lastEditor}` }}
            </div>
          </div>
          <ul
            style="list-style-type: none;"
            class="pa-0"
          >
            <li
              v-for="(status, index) in statusHistory"
              :key="index"
              :class="{
                'status_history__item': true,
                [getStatusClass(status.display_status)]: true,
                'connected': index !== statusHistory.length - 1
              }"
            >
              <v-tooltip
                top
                allow-overflow
                :open-on-click="true"
              >
                <template v-slot:activator="{ on, content, attrs }">
                  <div class="body-2 black--text">
                    <v-icon
                      v-if="status.status.includes('-')"
                      v-clipboard:copy="JSON.stringify(status.status_metadata)"
                      small
                      color="secondary"
                      icon
                      v-on="content"
                      @click.stop="() =>{}"
                    >
                      mdi-content-paste
                    </v-icon>

                    <span
                      v-if="status.status.includes('-')"
                      v-bind="attrs"
                      v-on="on"
                    >
                      {{ status.status }}
                    </span>
                    <span
                      v-else
                    >
                      {{ status.status }}
                    </span>
                  </div>
                </template>
                <span>
                  <vue-json-pretty
                    :data="formatJson(status)"
                    class="font-weight-black"
                  />
                </span>
              </v-tooltip>
              <div class="body-2 font-weight-bold black--text">
                {{ index === 0 || index === statusHistory.length - 1 ? formatDate(status.start_date, { timeZone: true }) : status.diff_for_humans }}
              </div>
            </li>
          </ul>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import { formatDate } from '@/utils/dates'
import permissions from '@/mixins/permissions'
import statusHistoryDialog from '@/mixins/status_history_dialog'
import 'vue-json-pretty/lib/styles.css'
import VueJsonPretty from 'vue-json-pretty'
import events from '@/enums/events'

export default {
  name: 'StatusHistoryDialog',

  components: { VueJsonPretty },

  mixins: [permissions, statusHistoryDialog],

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
    statusHistory: [],
    loading: false,
    useSystemStatus: vm.hasPermission('system-status-filter')
  }),

  computed: {
    userWhoUploadedTheRequest () {
      return this.order.upload_user_name ? this.order.upload_user_name : this.order.email_from_address
    },

    historyLabels () {
      return {
        companyName: this.order.company?.name || '---',
        variantName: this.order.variant_name || '---',
        variantId: this.order.variant_id || '---',
        lastEditor: this.order.ocr_data?.fields?.last_editor?.value || '---'
      }
    }
  },

  watch: {
    open () {
      if (this.open) {
        this.$root.$on(events.orderStatusUpdated, this.orderStatusUpdated)
      }

      if (this.open && this.statusHistory.length > 0) {
        return
      }

      if (!this.open) {
        this.$root.$off(events.orderStatusUpdated, this.orderStatusUpdated)
        return
      }

      this.fetchHistory()
    },

    useSystemStatus () {
      this.fetchHistory()
    }
  },
  methods: {
    formatDate,

    async fetchHistory () {
      await this.fetchStatusHistory({ order_id: this.order.id })
    },

    orderStatusUpdated (latestStatus) {
      if (this.order.id !== latestStatus.order_id) {
        return
      }

      this.updateStatusHistory(latestStatus)
    },
  }
}
</script>
<style lang="scss" scoped>
.status_history__item {
  @import "@/assets/styles/components/_statuses.scss";
  position: relative;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 0 rem(20) rem(32);

  &::after,
  &.connected::before {
    content: "";
    position: absolute;
    left: 0;
    // top: 0;
    background-color: var(--v-slate-gray-base);
  }

  &::after {
    height: rem(16);
    width: rem(16);
    border-radius: 50%;
  }

  &.connected::before {
    height: 100%;
    width: rem(2);
    left: rem(7);
    top: rem(16)
  }
}
</style>
