import axios from '@/store/api_calls/config/axios'
import toParams from '@/utils/to_params'

export const getTenantConfig = async () => axios.get('/api/current-tenant').then(data => [undefined, data.data]).catch(e => [e])

export const getDictionaryItems = async (filters = {}) => axios.get(`/api/dictionary-items?${toParams(filters)}`).then(data => [undefined, data.data]).catch(e => [e])
