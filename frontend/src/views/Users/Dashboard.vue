<template>
  <div class="row">
    <div class="col-2">
      <SidebarNavigation />
    </div>
    <div class="col-10">
      <UserList />
    </div>
  </div>
</template>

<script>
import SidebarNavigation from '@/components/General/SidebarNavigation'
import UserList from '@/components/Users/UserList'
export default {
  components: {
    SidebarNavigation,
    UserList
  }
}
</script>
<style lang="sass" scoped>

</style>
