<template>
  <div
    :class="`sidebar ${isMobile && 'mobile'}`"
  >
    <div
      v-if="!isMobile"
      class="sidebar__logo"
    />

    <div :class="`sidebar__body ${isMobile && 'mobile'}`">
      <v-btn
        color="primary"
        outlined
        width="115px"
        @click="goToOrdersList()"
      >
        <v-icon>
          mdi-chevron-left
        </v-icon>
        Order List
      </v-btn>

      <DetailsSidebarNavigation v-if="!isMobile" />

      <v-btn
        v-if="hasPermissions('orders-edit')"
        :color="saveBtnStyles"
        :outlined="!editMode && !isMobile"
        :style="{ marginBottom: '10px' }"
        test-id="toggle-btn"
        width="115px"
        @click="toggleEdit"
      >
        {{ editMode ? 'Save' : 'Edit Order' }}
      </v-btn>

      <v-btn
        v-if="!isMobile && hasPermissions('tms-submit')"
        color="primary"
        outlined
        width="115px"
        :disabled="disabled"
        @click="postSendToTms"
      >
        Send to   TMS
      </v-btn>
    </div>

    <div
      v-if="!isMobile"
      class="sidebar__footer"
    >
      <v-btn
        color="primary"
        outlined
        width="115px"
        class="logout__btn"
        @click="logoutBtn"
      >
        Logout
      </v-btn>
    </div>
  </div>
</template>

<script>
import isMobile from '@/mixins/is_mobile'
import hasPermissions from '@/mixins/permissions'
import { mapActions, mapState } from 'vuex'
import orders, { types } from '@/store/modules/orders'
import orderForm, { types as orderFormTypes } from '@/store/modules/order-form'
import { reqStatus } from '@/enums/req_status'
import utils, { type } from '@/store/modules/utils'
import DetailsSidebarNavigation from './DetailsSidebarNavigation'

export default {
  name: 'DetailsSidebar',

  components: { DetailsSidebarNavigation },

  mixins: [isMobile, hasPermissions],
  data () {
    return {
      message: '',
      dialog: false,
      modaltype: '',
      disabled: false
    }
  },

  computed: {
    ...mapState(orderForm.moduleName, {
      editMode: state => state.editMode
    }),

    saveBtnStyles () {
      if (this.isMobile) return 'secondary'
      if (this.editMode) return 'success'
      return 'primary'
    }
  },

  destroyed () {
    localStorage.removeItem('prevListUrl')
  },

  methods: {
    ...mapActions(orders.moduleName, [types.postSendToTms]),
    ...mapActions(orderForm.moduleName, {
      toggleEdit: orderFormTypes.toggleEdit
    }),
    ...mapActions('AUTH', ['logout']),
    ...mapActions(utils.moduleName, [type.setSnackbar]),

    async logoutBtn () {
      this.logoutError = false
      const status = await this.logout()
      if (status) {
        this.$router.push('/login')
      }
    },

    async postSendToTms () {
      const status = await this[types.postSendToTms]({ order_id: this.$route.params.id, status: 'sending-to-wint' })
      this.dialog = false
      if (status === reqStatus.success) {
        await this[type.setSnackbar]({
          show: true,
          showSpinner: false,
          message: 'Processing'
        })
      } else {
        await this[type.setSnackbar]({
          show: true,
          showSpinner: false,
          message: 'Some of your addresses are not validated'
        })
      }
      this.disabled = true
    },

    goToOrdersList () {
      const prevListUrl = localStorage.getItem('prevListUrl')

      if (prevListUrl) return this.$router.push(prevListUrl)
      this.$router.push('/')
    }
  }
}
</script>

<style lang="scss" scoped>

$ordermaster-logo: url("../../assets/images/ordermaster_logo.svg");

.sidebar {
  overflow-y: auto;
  z-index: 1;
  display: flex;
  position: fixed;
  left: 0;
  width: rem(map-get($sizes, sidebar-desktop-width));
  height: 100vh;
  flex-direction: column;
  align-items: center;
  background-color: map-get($colors, grey);
  box-shadow: map-get($properties, inset-shadow-right);
  padding-top: rem(40);
  padding-bottom: rem(30);

  &.mobile {
    height: rem(70);
    bottom: 0;
    width: 100vw;
    padding: unset;
    box-shadow: 0 rem(-4) rem(10) rem(-8) rgba(0,0,0,0.75);
  }
}

// .sidebar__logo {
//   width: 14rem;
//   min-height: 4.3rem;
//   height: 4.3rem;
//   // background-image: $cushing-logo;
//   background-size: 14rem 4.3rem;
//   background-position: center center;
// }

.sidebar__body {
  margin-top: rem(60);
  margin-bottom: auto;
  display: flex;
  flex-direction: column;
  align-items: center;

  &.mobile {
    height: 100%;
    width: 100%;
    margin: unset;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    padding: 0 rem(20);

    button {
      margin: unset!important;
    }
  }
}

.sidebar__footer {
  width: rem(111);
  height: rem(45);
  min-height: rem(45);
  margin-top: rem(60);
  background-image: $ordermaster-logo;
  background-size: contain;
  background-position: center center;
  display: flex;
  flex-direction: column-reverse;
  .logout__btn{
    margin: rem(50) auto;
  }
}
</style>
