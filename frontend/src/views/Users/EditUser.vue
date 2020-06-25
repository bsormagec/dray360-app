<template>
  <div class="row">
    <div class="col-2">
      <SidebarNavigation />
    </div>
    <div class="col-10">
      <EditUserForm />
    </div>
  </div>
</template>

<script>
import SidebarNavigation from '@/components/General/SidebarNavigation'
import EditUserForm from '@/components/Users/EditUserForm'
export default {
  components: {
    SidebarNavigation,
    EditUserForm
  }
}
</script>
<style lang="sass" scoped>

</style>
