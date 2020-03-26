import axios from '@/store/api_calls/axios_config'

export const getOrders = async () => axios.get('/api/orders').then(data => [undefined, data.data]).catch(e => [e])
