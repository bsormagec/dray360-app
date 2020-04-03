import axios from '@/store/api_calls/config/axios'

export const getOrders = async (page) => axios.get(`/api/orders?page=${page}`).then(data => [undefined, data.data]).catch(e => [e])
