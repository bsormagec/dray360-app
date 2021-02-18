import axios from '@/store/api_calls/config/axios'
import toParams from '@/utils/to_params'

export const getSearchAddress = async (filters) => axios.get(`/api/search-address?${toParams(filters)}`, { timeout: 28000 }).then(data => [undefined, data.data]).catch(e => [e])
