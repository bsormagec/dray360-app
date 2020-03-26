import axios from '@/store/api_calls/axios_config'

export const getOrders = async (page) => axios.get(`/api/orders?page=${page}`).then(data => [undefined, data.data]).catch(e => [e])
