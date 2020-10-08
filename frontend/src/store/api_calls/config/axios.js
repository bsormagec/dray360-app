import Axios from 'axios'

const axios = Axios.create({
  baseURL: `${process.env.VUE_APP_APP_URL}`,
  withCredentials: true
})

export default axios
