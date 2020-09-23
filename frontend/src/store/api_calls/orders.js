import axios from '@/store/api_calls/config/axios'
import toParams from '@/utils/to_params'

export const getOrders = async (filters, query) => axios.ext.get(`/api/orders?${toParams(filters)}`).then(data => [undefined, data.data]).catch(e => [e])

export const getOrderDetail = async (order) => axios.ext.get(`/api/orders/${order}`).then(data => [undefined, data.data]).catch(e => [e])

export const updateOrderDetail = async ({ id, changes }) => axios.ext.put(`/api/orders/${id}`, changes).then(data => [undefined, data.data]).catch(e => [e])

export const postUploadPDF = async (file) => axios.ext.post('/api/createocrrequestuploaduri', { filename: file.name, withCredentials: false })
  .then(response => {
    const config = {
      withCredentials: false,
      headers: {
        'content-type': 'multipart/form-data',
        'Access-Control-Allow-Origin': '*'
      }
    }

    return axios.ext.put(response.data.upload_uri, file, config).then(response => [undefined, response.data])
      .catch(e => [e])
  })
  .catch(e => [e])

export const getDownloadPDF = async (orderId) => axios.ext.get(`/api/orders/${orderId}/download-pdf`).then(data => console.log('data.data: ', data.data)).catch(e => [e])

export const postSendToTms = async (tmsData) => axios.ext.post('/api/send-to-tms', tmsData).then(data => [undefined, data]).catch(e => [e])
