<template>
  <v-data-table
    dense
    :headers="headers"
    :items="filteredAudits"
    :height="height"
    :header-props="{ sortIcon: 'mdi-chevron-up'}"
    class="elevation-1"
    fixed-header
    disable-pagination
    hide-default-footer
    :options="{
      sortBy: ['updated_at'],
      sortDesc: [true],
    }"
  >
    <template
      v-slot:body="{ items }"
    >
      <tbody>
        <tr
          v-for="(audit, index) in items"
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
            <pre>{{ audit.event === 'deleted' ? "\u003cdeleted\u003e" : audit.new }}</pre>
          </td>
          <td
            class="caption black--text"
          >
            {{ formatDate(audit.updated_at, { timeZone: true, withSeconds: true }) }}
          </td>
          <td class="caption black--text">
            {{ audit.user }}
          </td>
        </tr>
        <tr v-if="audits.length !== filteredAudits.length || showHidden">
          <td
            class="caption black--text text-center"
            colspan="6"
            style="cursor: pointer"
            @click="showHidden = !showHidden"
          >
            {{ showHidden ? 'Hide attributes' : 'Show hidden attributes' }}
          </td>
        </tr>
      </tbody>
    </template>
  </v-data-table>
</template>
<script>
import { formatDate } from '@/utils/dates'

export default {
  name: 'AuditLogsTable',
  props: {
    height: {
      type: String,
      required: false,
      default: '55vh',
    },
    audits: {
      type: Array,
      required: true,
      default: () => [],
    },
  },
  data: (vm) => ({
    showHidden: false,
    headers: [
      { text: 'Object', value: 'model_type' },
      { text: 'Attribute', value: 'attribute' },
      { text: 'From', value: 'old', sort: vm.sortValues },
      { text: 'To', value: 'new', sort: vm.sortValues },
      { text: 'Date', value: 'updated_at' },
      { text: 'User', value: 'user' },
    ],
  }),
  computed: {
    filteredAudits () {
      if (this.showHidden) {
        return this.audits
      }

      return this.audits.filter(item => {
        const hiddenAttributes = ['_verified']
        let containsIt = false

        hiddenAttributes.forEach(attribute => {
          if (item.attribute.includes(attribute)) {
            containsIt = true
          }
        })

        return !containsIt
      })
    }
  },

  methods: {
    formatDate,
    sortValues (a, b) {
      let newA = a === null ? '' : String(a).trim()
      let newB = b === null ? '' : String(b).trim()

      newA = newA === 'undefined' ? '<deleted>' : newA
      newB = newB === 'undefined' ? '<deleted>' : newB

      return !isNaN(+newA) && !isNaN(+newB)
        ? Number(newA) - Number(newB)
        : newA.localeCompare(newB)
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
