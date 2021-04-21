import axios from '@/store/api_calls/config/axios'
import toParams from '@/utils/to_params'

export const getDictionaryItems = async (filters = {}) => axios.get(`/api/dictionary-items?${toParams(filters)}`).then(data => [undefined, data.data]).catch(e => [e])

export const createDictionaryItem = async (data) => axios.post('/api/dictionary-items', data).then(data => [undefined, data.data]).catch(e => [e])
