<template>
  <div class="pa-5">
    <UserForm add />
  </div>
</template>

<script>
import UserForm from './UserForm'
import isMobile from '@/mixins/is_mobile'
import isMedium from '@/mixins/is_medium'

import utils, { actionTypes } from '@/store/modules/utils'
import { mapActions } from 'vuex'

export default {
  name: 'AddUser',
  components: {
    UserForm
  },
  mixins: [isMobile, isMedium],
  watch: {
    isMedium: function (newVal, oldVal) {
      if (!newVal) this.setSidebar({ show: true })
    },
    isMobile: function (newVal, oldVal) {
      if (newVal) this.setSidebar({ show: false })
      else this.setSidebar({ show: true })
    }
  },
  beforeMount () {
    if (!this.isMobile) return this.setSidebar({ show: true })
    return this.setSidebar({ show: false })
  },
  methods: {
    ...mapActions(utils.moduleName, [actionTypes.setSidebar])
  }
}
</script>
<style lang="sass" scoped>

</style>
