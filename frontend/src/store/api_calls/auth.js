import axios from '@/store/api_calls/config/axios'

export const getCsrfCookie = async () => axios.get('/sanctum/csrf-cookie').then(data => [undefined, data]).catch(e => [e])

export const getUser = async () => axios.get('api/user').then(data => [undefined, data.data]).catch(e => [e])

export const postSignUp = async (signUpData) => axios.post('/api/signup', signUpData).then(data => [undefined, data]).catch(e => [e])

export const postLogin = async (authData) => axios.post('/api/login', authData).then(data => [undefined, data]).catch(e => [e])

export const postLogout = async () => axios.post('api/logout').then(data => [undefined, data.data]).catch(e => [e])
