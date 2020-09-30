import Axios from 'axios'
import waitForResponse from '@/utils/for_tests/wait_for_response'
import { getCsrfCookie, postLogin } from '@/store/api_calls/auth'

const axios = Axios.create({
  withCredentials: true
})

if (process.env.NODE_ENV === 'test') {
  axios.interceptors.request.use((config) => {
    waitForResponse.initRequest()
    return config
  })
  axios.interceptors.response.use((response) => {
    waitForResponse.finishRequest()
    return response
  })
}

axios.defaults.baseURL = `${process.env.VUE_APP_APP_URL}`

// Forward to application downtime page if the server is currently under maintenance
axios.interceptors.response.use((response) => {
  return response
}, function (error) {
  if (error.response.status === 503) {
    window.location.href = axios.defaults.baseURL + '/application-downtime'
    console.log('application downtime')
  }
  return Promise.reject(error.response)
})

const axiosConfigs = {
  ori: axios,
  ext: {
    get: async (...args) => testExtHandler(async () => axios.get(...args)),
    post: async (...args) => testExtHandler(async () => axios.post(...args)),
    put: async (...args) => testExtHandler(async () => axios.put(...args)),
    delete: async (...args) => testExtHandler(async () => axios.delete(...args)),
    patch: async (...args) => testExtHandler(async () => axios.patch(...args))
  }
}

export default axiosConfigs

async function testExtHandler (cb) {
  if (process.env.NODE_ENV === 'test') {
    try {
      const res = await cb()
      if (!res) throw new Error()
      return res
    } catch (e) {
      await getCsrfCookie()
      await postLogin({ email: process.env.VUE_APP_TEST_USER_EMAIL, password: process.env.VUE_APP_TEST_USER_PASSWORD })

      const res = await cb()
      if (!res) return console.log('Failed twice auth')
      return res
    }
  } else {
    return await cb()
  }
}
