import axios from '@/store/api_calls/config/axios'
import toParams from '@/utils/to_params'

export const getAccountingMetrics = async (filters, query) => axios.get(`/api/metrics?${toParams(filters)}`)
  .then(data => [undefined, data.data])
  .catch(e => [e])
