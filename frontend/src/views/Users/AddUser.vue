<template>
  <div class="pa-5">
    <UserForm add />
  </div>
</template>

<script>
import UserForm from './UserForm'

import utils, { type } from '@/store/modules/utils'
import { mapActions } from 'vuex'

export default {
  name: 'AddUser',
  components: {
    UserForm
  },
  async beforeMount () {
    await this.setSidebar({ show: true })
  },
  methods: {
    ...mapActions(utils.moduleName, { setSidebar: type.setSidebar })
  }
}
</script>
<style lang="sass" scoped>

</style>
