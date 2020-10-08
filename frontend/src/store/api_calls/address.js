import axios from '@/store/api_calls/config/axios'
import toParams from '@/utils/to_params'

export const getSearchAddress = async (filters) => axios.get(`/api/search-address?${toParams(filters)}`).then(data => [undefined, data]).catch(e => [e])
