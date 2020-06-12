import axios from '@/store/api_calls/config/axios'

export const getLibrary = async () => axios.get('/api/ocr/rules').then(data => [undefined, data.data]).catch(e => [e])
export const getAccountVariantRules = async () => axios.get('/api/ocr/rules-assignment?account_id=8&variant_id=8').then(data => [undefined, data.data]).catch(e => [e])
