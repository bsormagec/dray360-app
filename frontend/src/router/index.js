import Vue from 'vue'
import VueRouter from 'vue-router'
import Orders from '@/views/Orders/Orders'
import Details from '@/views/Details/Details'
import Login from '@/views/Login'
import RulesEditor from '@/views/RulesEditor/RulesEditor'
import auth from '@/router/middleware/auth'
import StyleGuide from '@/views/StyleGuide'
import Dashboard from '@/views/Users/Dashboard'
import AddUser from '@/views/Users/AddUser'
import EditUser from '@/views/Users/EditUser'
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
  // TODO: Parametrize user ID
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
  // TODO: Decide if these 2 routes should be a single one or not
  {
    path: '/styleguide',
    name: 'StyleGuide',
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
    path: '/rules-editor',
    name: 'RulesEditor',
    meta: {
      middleware: [auth]
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
