import Vue from 'vue'
import VueRouter from 'vue-router'
import Orders from '@/views/Orders/Orders'
import Details from '@/views/Details/Details'
import Login from '@/views/Login'
import RulesEditor from '@/views/RulesEditor/RulesEditor'
import auth from '@/router/middleware/auth'
import superadmin from '@/router/middleware/superadmin'
import permission from '@/router/middleware/permissions'
import StyleGuide from '@/views/StyleGuide'
import Dashboard from '@/views/Users/Dashboard'
import AddUser from '@/views/Users/AddUser'
import EditUser from '@/views/Users/EditUser'
import EditProfile from '@/views/Users/EditProfile'
import ChangePassword from '@/views/Users/ChangePassword'
import { runMiddleware } from '@/router/middleware'
import PageNotFound from '@/views/PageNotFound'
import MappingField from '@/views/Mappings/MappingField'
import LoggedOut from '@/router/middleware/LoggedOut'
import AccesorialsMapping from '@/views/Mappings/AccesorialsMapping'

Vue.use(VueRouter)

const routes = [
  {
    path: '/dashboard/',
    name: 'Orders',
    meta: {
      middleware: [auth, permission('orders-view')]
    },
    component: Orders
  },
  {
    path: '/user/dashboard',
    name: 'Dashboard',
    meta: {
      middleware: [auth, permission('users-view')]
    },
    component: Dashboard
  },
  {
    path: '/user/dashboard/add-user',
    name: 'AddUser',
    meta: {
      middleware: [auth, permission('users-view')]
    },
    component: AddUser
  },
  {
    path: '/user/dashboard/edit-user/:id',
    name: 'EditUser',
    meta: {
      middleware: [auth, permission('users-view')]
    },
    component: EditUser
  },
  {
    path: '/user/dashboard/edit-profile',
    name: 'EditProfile',
    meta: {
      middleware: [auth, permission('users-view')]
    },
    component: EditProfile
  },
  {
    path: '/user/dashboard/change-password',
    name: 'ChangePassword',
    meta: {
      middleware: [auth, permission('users-view')]
    },
    component: ChangePassword
  },
  {
    path: '/styleguide',
    name: 'StyleGuide',
    meta: {
      middleware: [auth]
    },
    component: StyleGuide
  },
  {
    path: '/order/:id',
    name: 'Details',
    meta: {
      middleware: [auth]
    },
    component: Details
  },
  {
    path: '/login',
    name: 'Login',
    alias: '/',
    meta: {
      middleware: [LoggedOut]
    },
    component: Login
  },
  {
    path: '/rules-editor',
    name: 'RulesEditor',
    meta: {
      middleware: [auth, superadmin]
    },
    component: RulesEditor
  },
  {
    path: '/companies/:id/refs-custom-mapping',
    name: 'Mapping',
    component: MappingField,
    meta: {
      middleware: [auth]
    }
  },
  {
    path: '/companies/:id/billing-mapping',
    name: 'Billing Mapping',
    component: AccesorialsMapping,
    meta: {
      middleware: [auth]
    }
  },
  {
    path: '*',
    name: 'Not Found',
    component: PageNotFound
  }
]

const router = new VueRouter({
  mode: 'history',
  routes
})

router.beforeEach(runMiddleware)

export default router
