import axios from '@/store/api_calls/config/axios'
import toParams from '@/utils/to_params'

export const getFieldMaps = async (filters = {}) => axios.get(`/api/field-maps?${toParams(filters)}`).then(data => [undefined, data.data]).catch(e => [e])

export const createFieldMaps = async (postData) => axios.post('/api/field-maps', postData).then(data => [undefined, data.data]).catch(e => [e])
