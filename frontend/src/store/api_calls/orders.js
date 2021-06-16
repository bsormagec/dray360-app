import axios from '@/store/api_calls/config/axios'
import toParams from '@/utils/to_params'

export const getOrders = async (filters, query) => axios.get(`/api/orders?${toParams(filters)}`).then(data => [undefined, data.data]).catch(e => [e])

export const getOrderDetail = async (order) => axios.get(`/api/orders/${order}`).then(data => [undefined, data.data]).catch(e => [e])

export const updateOrderDetail = async ({ id, changes }) => axios.put(`/api/orders/${id}`, changes).then(data => [undefined, data.data]).catch(e => [e])

export const updateAllOrders = async ({ id, changes, path = null }) => axios.put(`/api/orders/${id}/update-all`, { ...changes, change_path: path }).then(data => [undefined, data.data]).catch(e => [e])

export const postSendToTms = async (orderId) => axios.post(`/api/orders/${orderId}/send-to-tms`).then(data => [undefined, data]).catch(e => [e])

export const postSendToClient = async (orderId) => axios.post(`/api/orders/${orderId}/send-to-client`).then(data => [undefined, data]).catch(e => [e])

export const replicateOrder = async (orderId) => axios.post(`/api/orders/${orderId}/replicate`).then(data => [undefined, data]).catch(e => [e])

export const getDivisionCodes = async (companyId, tmsId) => axios.get(`/api/companies/${companyId}/tms-provider/${tmsId}/division-names`).then(data => [undefined, data.data]).catch(e => [e])

export const delDeleteOrder = async (orderId) => axios.delete(`/api/orders/${orderId}`).then(data => [undefined, data.data]).catch(e => [e])
