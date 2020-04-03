import Axios from 'axios'
import waitForResponse from '@/utils/for_tests/wait_for_response'

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

export default axios
