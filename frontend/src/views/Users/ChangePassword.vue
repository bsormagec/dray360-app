<template>
  <div class="row">
    <div class="col-2">
      <SidebarNavigation />
    </div>
    <div class="col-10">
      <ChangePasswordForm />
    </div>
  </div>
</template>

<script>
import SidebarNavigation from '@/components/General/SidebarNavigation'
import ChangePasswordForm from '@/components/Users/ChangePasswordForm'
export default {
  components: {
    SidebarNavigation,
    ChangePasswordForm
  }
}
</script>
<style lang="sass" scoped>

</style>
