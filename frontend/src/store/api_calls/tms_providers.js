import axios from '@/store/api_calls/config/axios'
import toParams from '@/utils/to_params'

export const getTmsProviders = async (filters = {}) => axios.get(`/api/tms-providers?${toParams(filters)}`).then(data => [undefined, data.data]).catch(e => [e])
