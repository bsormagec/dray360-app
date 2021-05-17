import axios from '@/store/api_calls/config/axios'
import toParams from '@/utils/to_params'

export const getRequests = async (filters) => axios.get(`/api/ocr/requests?${toParams(filters)}`).then(data => [undefined, data.data]).catch(e => [e])

export const deleteRequest = async (requestId) => axios.delete(`/api/ocr/requests/${requestId}`).then(data => [undefined, data.data]).catch(e => [e])

export const getSourceFileDownloadURL = async (requestId) => axios.get(`/api/ocr/requests/${requestId}/download-source-file`).then(data => [undefined, data.data]).catch(e => [e])

export const getSourceEmailDownloadURL = async (requestId) => axios.get(`/api/ocr/requests/${requestId}/download-source-email`).then(data => [undefined, data.data]).catch(e => [e])

export const getEmailDetails = async (requestId) => axios.get(`/api/ocr/requests/${requestId}/email-details`).then(data => [undefined, data.data]).catch(e => [e])

export const reprocessOcrRequest = async (requestId) => axios.post(`/api/ocr/requests/${requestId}/reprocess`).then(data => [undefined, data.data]).catch(e => [e])

export const reimportOcrRequestAbbyy = async (requestId) => axios.post(`/api/ocr/requests/${requestId}/reimport-abbyy`).then(data => [undefined, data.data]).catch(e => [e])

export const sendRequestOrdersToTms = async (requestId) => axios.post(`/api/ocr/requests/${requestId}/send-to-tms`).then(data => [undefined, data.data]).catch(e => [e])

export const changeRequestDoneStatus = async (requestId, data) => axios.put(`/api/ocr/requests/${requestId}/done-status`, data).then(data => [undefined, data.data]).catch(e => [e])

export const getPtImageRequestDetails = async (requestId) => axios.get(`/api/upload-pt-images/${requestId}`).then(data => [undefined, data.data]).catch(e => [e])

export const postUploadRequestFile = async (file, params) => axios.post('/api/ocr/requests', { filename: file.name, withCredentials: false, ...params })
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
