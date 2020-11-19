<template>
  <div>
    <AddUserForm />
  </div>
</template>

<script>

import AddUserForm from '@/components/Users/AddUserForm'
import utils, { type } from '@/store/modules/utils'
import { mapActions } from 'vuex'

export default {
  components: {
    AddUserForm
  },
  beforeMount () {
    this.showSidebar()
  },
  methods: {
    ...mapActions(utils.moduleName, [type.setSidebar]),

    async showSidebar () {
      await this[type.setSidebar]({
        show: true
      })
    }
  }
}
</script>
<style lang="sass" scoped>

</style>
