import axios from '@/store/api_calls/config/axios'
import toParams from '@/utils/to_params'

export const getRequests = async (filters) => axios.get(`/api/ocr/requests?${toParams(filters)}`).then(data => [undefined, data.data]).catch(e => [e])

export const deleteRequest = async (requestId) => axios.delete(`/api/ocr/requests/${requestId}`).then(data => [undefined, data.data]).catch(e => [e])

export const getSourceFileDownloadURL = async (requestId) => axios.get(`/api/ocr/requests/${requestId}/download-source-file`).then(data => [undefined, data.data]).catch(e => [e])

export const reprocessOcrRequest = async (requestId) => axios.post(`/api/ocr/requests/${requestId}/reprocess`).then(data => [undefined, data.data]).catch(e => [e])
