import axios from '@/store/api_calls/config/axios'

export const getLibrary = async () => axios.get('/api/ocr/rules').then(data => [undefined, data.data]).catch(e => [e])
