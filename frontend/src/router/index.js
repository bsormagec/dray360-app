import Vue from 'vue'
import VueRouter from 'vue-router'
import Orders from '@/views/Orders/Orders'
import Login from '@/views/Login'
import auth from '@/router/middleware/auth'
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
    path: '/login',
    name: 'Login',
    component: Login
  }
]

const router = new VueRouter({
  mode: 'history',
  routes
})

router.beforeEach(runMiddleware)

export default router
