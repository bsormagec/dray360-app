<template>
  <div class="pa-5">
    <UserForm edit />
  </div>
</template>

<script>
import UserForm from './UserForm'

import utils, { type } from '@/store/modules/utils'
import isMobile from '@/mixins/is_mobile'
import isMedium from '@/mixins/is_medium'
import { mapActions } from 'vuex'

export default {
  name: 'EditUser',

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
  async beforeMount () {
    await this.setSidebar({ show: true })
    if (!this.isMobile) return this.setSidebar({ show: true })
    return this.setSidebar({ show: false })
  },
  methods: {
    ...mapActions(utils.moduleName, { setSidebar: type.setSidebar })
  }
}
</script>
<style lang="sass" scoped>

</style>
