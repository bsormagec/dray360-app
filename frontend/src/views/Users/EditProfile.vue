<template>
  <div class="row">
    <div class="col-2">
      <SidebarNavigation />
    </div>
    <div class="col-10">
      <EditProfileForm />
    </div>
  </div>
</template>

<script>
import SidebarNavigation from '@/components/General/SidebarNavigation'
import EditProfileForm from '@/components/Users/EditProfileForm'

export default {
  components: {
    SidebarNavigation,
    EditProfileForm
  }

}
</script>
<style lang="sass" scoped>

</style>
