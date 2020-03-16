import Vue from 'vue';
import Router from 'vue-router';
import HelloWorld from '../components/HelloWorld.vue'
import Test from '../components/Test.vue'
Vue.use(Router);

const routes = [
    { path: '/test', component: HelloWorld },
    { path: '/test2', component: Test }
  ]

  // 3. Create the router instance and pass the `routes` option
  // You can pass in additional options here, but let's
  // keep it simple for now.
  const router = new Router({
    mode: 'history',
    routes // short for `routes: routes`
  });

  export default router;
