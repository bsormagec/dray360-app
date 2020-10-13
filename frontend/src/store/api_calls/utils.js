import axios from '@/store/api_calls/config/axios'

export const getTenantConfig = async () => axios.get('/api/current-tenant').then(data => [undefined, data.data]).catch(e => [e])
