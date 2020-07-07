import Vue from 'vue'
import VueRouter from 'vue-router'
import Orders from '@/views/Orders/Orders'
import Details from '@/views/Details/Details'
import Login from '@/views/Login'
import RulesEditor from '@/views/RulesEditor/RulesEditor'
import auth from '@/router/middleware/auth'
import superadmin from '@/router/middleware/superadmin'
import StyleGuide from '@/views/StyleGuide'
import Dashboard from '@/views/Users/Dashboard'
import AddUser from '@/views/Users/AddUser'
import EditUser from '@/views/Users/EditUser'
import EditProfile from '@/views/Users/EditProfile'
import ChangePassword from '@/views/Users/ChangePassword'
import { runMiddleware } from '@/router/middleware'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'Orders',
    meta: {
      middleware: [auth]
    },
    component: Orders
  },
  {
    path: '/user/dashboard',
    name: 'Dashboard',
    component: Dashboard
  },
  {
    path: '/user/dashboard/add-user',
    name: 'AddUser',
    component: AddUser
  },
  {
    path: '/user/dashboard/edit-user/:id',
    name: 'EditUser',
    component: EditUser
  },
  {
    path: '/user/dashboard/edit-profile/:id',
    name: 'EditProfile',
    component: EditProfile
  },
  {
    path: '/user/dashboard/change-password/:id',
    name: 'ChangePassword',
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
    component: Login
  },
  {
    path: '/rules-editor/company/:company_id/variant/:variant_id',
    name: 'RulesEditor',
    meta: {
      middleware: [superadmin]
    },
    component: RulesEditor
  }
]

const router = new VueRouter({
  mode: 'history',
  routes
})

router.beforeEach(runMiddleware)

export default router
