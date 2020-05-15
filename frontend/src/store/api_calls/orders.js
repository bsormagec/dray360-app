import axios from '@/store/api_calls/config/axios'

export const getOrders = async (page) => axios.get(`/api/orders?page=${page}`).then(data => [undefined, data.data]).catch(e => [e])

export const getOrderDetail = async (order) => axios.get(`/api/orders/${order}`).then(data => [undefined, data.data.ocr_data]).catch(e => [e])
