<template>
  <v-simple-table
    dense
    fixed-header
    :height="height"
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
          v-for="(audit, index) in filteredAudits"
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
  </v-simple-table>
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
  data: () => ({
    showHidden: false,
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
