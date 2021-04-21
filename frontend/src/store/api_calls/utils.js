import axios from '@/store/api_calls/config/axios'
import toParams from '@/utils/to_params'

export const getTenantConfig = async () => axios.get('/api/current-tenant').then(data => [undefined, data.data]).catch(e => [e])

export const getStatusHistory = async (filters = {}) => axios.get(`/api/status-history?${toParams(filters)}`).then(data => [undefined, data.data]).catch(e => [e])

export const getAuditLogs = async (filters = {}) => axios.get(`/api/audit-logs?${toParams(filters)}`).then(data => [undefined, data.data]).catch(e => [e])

export const getAuditLogsDashboard = async (filters = {}) => axios.get(`/api/audit-logs-dashboard?${toParams(filters)}`).then(data => [undefined, data.data]).catch(e => [e])

export const postUploadPtImageFile = async (file, params) => {
  // eslint-disable-next-line camelcase
  const { request_id, order_id, company_id } = params
  return axios.post('/api/file-upload-requests', { request_id, order_id, company_id, type: 'pt-imagetype', filename: file.name, withCredentials: false, })
    .then(({ data }) => {
      const { data: getUrlData } = data

      const config = {
        withCredentials: false,
        headers: {
          'content-type': 'multipart/form-data',
          'Access-Control-Allow-Origin': '*'
        }
      }

      return axios
        .put(getUrlData.upload_uri, file, config)
        .then(awsResponse => {
          // eslint-disable-next-line camelcase
          const { original_filename, uploading_filename, datetime_utciso, request_id: requestId } = getUrlData
          // eslint-disable-next-line camelcase
          const { tms_shipment_id, pt_image_type, } = params

          return axios.post('/api/upload-pt-images', { request_id: requestId, original_filename, uploading_filename, datetime_utciso, company_id, tms_shipment_id, pt_image_type, order_id })
            .then(thirdReponse => [undefined, thirdReponse.data])
            .catch(e => [e])
        })
        .catch(e => [e])
    }).catch(e => [e])
}
