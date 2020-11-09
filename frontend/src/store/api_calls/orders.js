import axios from '@/store/api_calls/config/axios'
import toParams from '@/utils/to_params'

export const getOrders = async (filters, query) => axios.get(`/api/orders?${toParams(filters)}`).then(data => [undefined, data.data]).catch(e => [e])

export const getOrders2 = async (filters, query) => axios.get(`/api/orders-2?${toParams(filters)}`).then(data => data.data).catch(e => [e])

export const getOrderDetail = async (order) => axios.get(`/api/orders/${order}`).then(data => [undefined, data.data]).catch(e => [e])

export const updateOrderDetail = async ({ id, changes }) => axios.put(`/api/orders/${id}`, changes).then(data => [undefined, data.data]).catch(e => [e])

export const postUploadPDF = async (file, variantName) => axios.post('/api/ocr/requests', { filename: file.name, withCredentials: false, variant_name: variantName })
  .then(response => {
    const config = {
      withCredentials: false,
      headers: {
        'content-type': 'multipart/form-data',
        'Access-Control-Allow-Origin': '*'
      }
    }

    return axios.put(response.data.upload_uri, file, config).then(response => [undefined, response.data])
      .catch(e => [e])
  })
  .catch(e => [e])

export const getDownloadPDFURL = async (orderId) => axios.get(`/api/orders/${orderId}/download-pdf`).then(data => [undefined, data.data]).catch(e => [e])

export const reprocessOcrRequest = async (requestId) => axios.post(`/api/ocr/requests/${requestId}/reprocess`).then(data => [undefined, data.data]).catch(e => [e])

export const postSendToTms = async (tmsData) => axios.post(`/api/orders/${tmsData.order_id}/send-to-tms`).then(data => [undefined, data]).catch(e => [e])

export const getDivisionCodes = async (companyId, tmsId) => axios.get(`/api/companies/${companyId}/tms-provider/${tmsId}/division-names`).then(data => [undefined, data.data]).catch(e => [e])

export const delDeleteOrder = async (orderId) => axios.delete(`/api/orders/${orderId}`).then(data => [undefined, data.data]).catch(e => [e])
