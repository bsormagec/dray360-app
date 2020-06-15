import axios from '@/store/api_calls/config/axios'

const config = {
  withCredentials: false,
  headers: {
    'content-type': 'multipart/form-data',
    'Access-Control-Allow-Origin': '*'
  }
}

export const getOrders = async (page) => axios.get(`/api/orders?page=${page}`).then(data => [undefined, data.data]).catch(e => [e])

export const getOrderDetail = async (order) => axios.get(`/api/orders/${order}`).then(data => [undefined, data.data]).catch(e => [e])

export const postUploadPDF = async (file) => axios.post('/api/createocrrequestuploaduri', { filename: file.name, withCredentials: false })
  .then(response => {
    axios.put(response.data.upload_uri, file, config)
      .then(response => [undefined, response.data])
      .catch(e => [e])
  })
  .catch(e => [e])
