<template>
  <div class="row">
    <div class="col-2">
      <SidebarNavigation />
    </div>
    <div class="col-10">
      <AddUserForm />
    </div>
  </div>
</template>

<script>
import SidebarNavigation from '@/components/General/SidebarNavigation'
import AddUserForm from '@/components/Users/AddUserForm'
export default {
  components: {
    SidebarNavigation,
    AddUserForm
  }
}
</script>
<style lang="sass" scoped>

</style>
