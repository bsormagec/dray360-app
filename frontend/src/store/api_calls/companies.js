import axios from '@/store/api_calls/config/axios'

export const updateCompanies = async ({ id, changes }) => axios.ext.put(`/api/companies/${id}`, changes).then(data => [undefined, data.data]).catch(e => [e])

export const getCompaniesbyId = async ({ id }) => axios.ext.get(`/api/companies/${id}`).then(data => [undefined, data.data]).catch(e => [e])
