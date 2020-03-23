import Vue from 'vue'
import Axios from 'axios'

Axios.defaults.baseURL = process.env.APP_URL

const axios = Axios.create({
  headers: {
    'content-type': 'application/json'
  }
})

// // Request interceptor
// axios.interceptors.request.use(
//     function(config) {
//         // Do something before request is sent
//         return config;
//     },
//     function(error) {
//         // Do something with request error
//         return Promise.reject(error);
//     }
// );

// // Response interceptor
// axios.interceptors.response.use(
//     function(response) {
//         // Do something with response data
//         return response;
//     },
//     function(error) {
//         // Do something with response error
//         return Promise.reject(error);
//     }
// );

Vue.use(Vue => {
  Vue.axios = axios
  Vue.prototype.$axios = axios
})

export default axios
