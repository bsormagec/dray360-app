import axios from '@/store/api_calls/axios_config'

export const getOrders = async () => axios.get('/orders').then(data => [undefined, data]).catch(e => [e])
