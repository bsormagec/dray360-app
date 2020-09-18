import axios from '@/store/api_calls/config/axios'

export const changePassword = async (oldPassword, password, passwordConfirmation) => axios.ext.post('/api/password/change', oldPassword, password, passwordConfirmation).then(data => [undefined, data.data]).catch(e => [e])
