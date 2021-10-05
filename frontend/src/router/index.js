import Vue from 'vue'
import VueRouter from 'vue-router'
import OrderDetails from '@/views/OrderDetails/OrderDetails'
import Login from '@/views/Login'
import Inbox from '@/views/Inbox/Inbox'
import Search from '@/views/Search/Search'
import RulesEditor from '@/views/RulesEditor/RulesEditor'
import auth from '@/router/middleware/auth'
import permission from '@/router/middleware/permissions'
import dev from '@/router/middleware/dev'
import StyleGuide from '@/views/StyleGuide'
import Dashboard from '@/views/Users/Dashboard'
import AddUser from '@/views/Users/AddUser'
import EditUser from '@/views/Users/EditUser'
import EditProfile from '@/views/Users/EditProfile'
import ChangePassword from '@/views/Users/ChangePassword'
import { runMiddleware } from '@/router/middleware'
import PageNotFound from '@/views/PageNotFound'
import PageNotAuthorized from '@/views/PageNotAuthorized'
import MappingField from '@/views/Mappings/MappingField'
import FieldMapping from '@/views/FieldMapping/FieldMapping'
import LoggedOut from '@/router/middleware/LoggedOut'
import ForgotPassword from '@/views/ForgotPassword'
import EmailConfirmation from '@/views/EmailConfirmation'
import ResetPassword from '@/views/ResetPassword'
import AuditLogs from '@/views/AuditLogs/AuditLogs'
import ApplicationDowntime from '@/views/ApplicationDowntime'
import AccountingDashboard from '@/views/AccountingDashboard'
import OrderCommentsDashboard from '@/views/OrderCommentsDashboard'

Vue.use(VueRouter)

const routes = [
  {
    path: '/inbox',
    name: 'Inbox',
    meta: {
      middleware: [auth, permission('orders-view')]
    },
    component: Inbox
  },
  {
    path: '/search',
    name: 'Search',
    meta: {
      middleware: [auth, permission('orders-view')]
    },
    component: Search
  },
  {
    path: '/dashboard',
    redirect: '/inbox'
  },
  {
    path: '/user/dashboard',
    name: 'Manage Users',
    meta: {
      middleware: [auth, permission('users-view')]
    },
    component: Dashboard
  },
  {
    path: '/audit-logs',
    name: 'Audit Logs',
    meta: {
      middleware: [auth, permission('audit-logs-view')]
    },
    component: AuditLogs,
  },
  {
    path: '/user/dashboard/add-user',
    name: 'Add User',
    meta: {
      middleware: [auth, permission('users-view')]
    },
    component: AddUser
  },
  {
    path: '/user/dashboard/edit-user/:id',
    name: 'Edit User',
    meta: {
      middleware: [auth, permission('users-view')]
    },
    component: EditUser
  },
  {
    path: '/user/edit-profile',
    name: 'Edit Profile',
    meta: {
      middleware: [auth]
    },
    component: EditProfile
  },
  {
    path: '/user/dashboard/change-password',
    name: 'Change Password',
    meta: {
      middleware: [auth]
    },
    component: ChangePassword
  },
  {
    path: '/styleguide',
    name: 'Style Guide',
    meta: {
      middleware: [auth, dev]
    },
    component: StyleGuide
  },
  {
    path: '/order/:id',
    name: 'Order Details',
    meta: {
      middleware: [auth]
    },
    component: OrderDetails
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
    name: 'Rules Editor',
    meta: {
      middleware: [auth, permission('rules-editor-view')]
    },
    component: RulesEditor
  },
  {
    path: '/companies/refs-custom-mapping',
    name: 'Mapping',
    component: MappingField,
    meta: {
      middleware: [auth]
    }
  },
  {
    path: '/field-mapping',
    name: 'Field Mapping Portal',
    component: FieldMapping,
    meta: {
      middleware: [auth, permission('field-maps-view')]
    }
  },
  {
    path: '*',
    name: 'Not Found',
    component: PageNotFound
  },
  {
    path: '/not-authorized',
    name: 'Not Authorized',
    component: PageNotAuthorized
  },
  {
    path: '/forgot-password',
    name: 'Forgot Password',
    component: ForgotPassword,
    meta: {
      middleware: [LoggedOut]
    }
  },
  {
    path: '/email-confirmation',
    name: 'Email Confirmation',
    component: EmailConfirmation,
    meta: {
      middleware: [LoggedOut]
    }
  },
  {
    path: '/password/reset/:id',
    name: 'Reset Password',
    component: ResetPassword,
    meta: {
      middleware: [LoggedOut]
    }
  },
  {
    path: '/application-downtime',
    name: 'Application Downtime',
    component: ApplicationDowntime
  },
  {
    path: '/accounting-dashboard',
    name: 'Accounting Dashboard',
    component: AccountingDashboard,
    meta: {
      middleware: [auth]
    }
  },
  {
    path: '/comments-logs',
    name: 'Comments Logs',
    component: OrderCommentsDashboard,
    meta: {
      middleware: [auth, permission('orders-view')]
    }
  }
]

const router = new VueRouter({
  mode: 'history',
  routes
})

router.beforeEach(runMiddleware)

export default router
