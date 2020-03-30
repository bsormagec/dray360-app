import Axios from 'axios'

const axios = Axios.create({
  withCredentials: true
})

axios.defaults.baseURL = `${process.env.VUE_APP_APP_URL}`

export default axios
