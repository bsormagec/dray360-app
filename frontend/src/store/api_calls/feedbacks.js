import axios from '@/store/api_calls/config/axios'
import toParams from '@/utils/to_params'

export const addFeedback = async (body) => axios.post('/api/feedbacks', body)
  .then(data => [undefined, data.data])
  .catch(e => [e])

export const getFeedback = async (filters) => axios.get(`/api/feedbacks?${toParams(filters)}`)
  .then(data => [undefined, data.data])
  .catch(e => [e])
