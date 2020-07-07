<template>
  <div>
    <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
    <v-navigation-drawer
      v-model="drawer"
      permanent
      class="sidebar__nav"
      absolute
      bottom
    >
      <img
        src="@/assets/images/dry360_logo.png"
        class="logo__dry"
        alt=""
      >
      <div class="menu">
        <v-list
          nav
          dense
        >
          <v-list-item-group v-model="group">
            <v-list-item
              v-for="(item, i) in items"
              :key="i"
              :to="item.path"
            >
              <v-list-item-icon>
                <v-icon v-text="item.icon" />
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title
                  :key="i"

                  v-text="item.text"
                />
              </v-list-item-content>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </div>
      <img
        src="@/assets/images/LogoDryPoweredBy.svg"
        class="logo__dry_bottom"
        alt=""
      >
    </v-navigation-drawer>
  </div>
</template>
<script>
export default {
  data () {
    return {
      drawer: false,
      group: null,

      items: [
        {
          text: 'Dashboard',
          path: '/user/Dashboard'
        },
        {
          text: 'Manage Users'
        },
        {
          text: 'My Profile'
        },
        {
          text: 'Logout'
        }
      ],
      model: 1
    }
  },
  watch: {
    group () {
      this.drawer = false
    }
  }

}
</script>

<style lang="scss" scoped>

$sidebarbackground: url("../../assets/images/sidebarbackground .png");
    .sidebar__nav{
        background: linear-gradient(90deg, rgba(0, 60, 113, 0.1) 0%, rgba(0, 60, 113, 0.05) 31.77%, rgba(0, 60, 113, 0) 100%);
        box-shadow: inset -1px 0px 0px rgba(0, 60, 113, 0.03);
        display: flex;
        flex-direction: column;
        align-content: center;
        align-self: center;
        background-image: $sidebarbackground;
        background-size: 100% 100vh;

        .logo__dry{
            width: 12rem;
            height: 4.8rem;
            margin: 5rem auto;
            display: block;

        }
        .logo__dry_bottom{
            @include center;
            bottom: 3rem;

        }
        .menu{
            .v-list-item:not(:last-child){
                border-bottom: 0.2rem solid map-get($colors, gray );
            }
            .v-list-item__title{
                color: map-get($colors, mainblue ) !important;
                text-transform: uppercase;
                font-weight: 500;
                text-align: right;
                letter-spacing: 0.75px;
                padding: 2.2rem 0px;
            }
            & > div{
                background: transparent !important;
            }
        }
    }

</style>
