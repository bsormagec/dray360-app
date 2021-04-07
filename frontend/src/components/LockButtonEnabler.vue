<template>
  <v-tooltip bottom>
    <template v-slot:activator="{ on, attrs }">
      <v-btn
        outlined
        dense
        small
        icon
        color="primary"
        :class="className"
        v-bind="attrs"
        @click="toggleSupervise"
        v-on="on"
      >
        <v-icon small>
          {{ supervise ? 'mdi-eye' : 'mdi-lock' }}
        </v-icon>
      </v-btn>
    </template>
    <span>{{ supervise ? 'Enable auto-locking requests' : 'Disable auto-locking requests' }}</span>
  </v-tooltip>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import requestsList, { types as requestsListTypes } from '@/store/modules/requests-list'

export default {
  name: 'LockButtonEnabler',

  props: ['className'],

  computed: {
    ...mapState(requestsList.moduleName, {
      supervise: state => state.supervise,
    }),
  },
  methods: {
    ...mapActions(requestsList.moduleName, {
      toggleSupervise: requestsListTypes.toggleSupervise,
    }),
  }
}
</script>

<style lang="scss" scoped>
</style>
