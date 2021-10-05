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
          <span class="secondary--text d-flex">
            '{{ selectedField }}' audit info
          </span>
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
          <AuditLogsTable :audits="currentAudits" />
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import get from 'lodash/get'
import deepDiff from 'deep-diff'
import flatten from 'lodash/flatten'

import { mapState } from 'vuex'
import fieldMaps from '@/store/modules/field_maps'

import AuditLogsTable from '@/components/AuditLogsTable'

export default {
  name: 'FieldMapAuditDialog',
  components: { AuditLogsTable },
  props: {
    open: {
      type: Boolean,
      required: true
    },
    selectedField: {
      type: String,
      required: true
    }
  },
  data: (vm) => ({
    showHidden: false,
  }),

  computed: {
    ...mapState(fieldMaps.moduleName, { audits: state => state.audits }),

    currentAudits () {
      return flatten(this.audits.map(audit => {
        const oldValue = get(audit, `old.${this.selectedField}`, {})
        const newValue = get(audit, `new.${this.selectedField}`, {})

        // eslint-disable-next-line camelcase
        const { user, updated_at } = audit
        const diffs = deepDiff(oldValue, newValue)

        if (!diffs) {
          return []
        }

        return diffs
          .filter(diff => !(diff.kind === 'N' && !diff.lhs && !diff.rhs))
          .map(diff => ({
            user,
            updated_at,
            model_type: 'field_map',
            attribute: diff.path.join(''),
            old: diff.lhs,
            new: diff.rhs,
          }))
      }))
    },
  },

  methods: {
  }
}
</script>
<style lang="scss" scoped>
</style>
