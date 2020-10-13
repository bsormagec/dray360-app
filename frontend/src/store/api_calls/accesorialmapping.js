import axios from '@/store/api_calls/config/axios'

export const updateAccesorialMapping = async ({ variant, id, mapping }) => axios.put(`/api/companies/${id}/variants/${variant}/accesorials`, { 'billing-mapping': mapping }).then(data => [undefined, data.data]).catch(e => [e])

export const getAccesorialMapping = async ({ variant, id }) => axios.get(`/api/companies/${id}/variants/${variant}/accesorials`).then(data => [undefined, data.data]).catch(e => [e])
