import qs from 'query-string'
import axios from '@/store/api_calls/config/axios'

export const getCsrfCookie = async () => axios.ori.get('/sanctum/csrf-cookie').then(data => [undefined, data]).catch(e => [e])

export const getUser = async () => axios.ori.get('/api/user').then(data => [undefined, data.data]).catch(e => [e])

export const postSignUp = async (signUpData) => axios.ori.post('/api/signup', signUpData).then(data => [undefined, data]).catch(e => [e])

export const postLogin = async (authData) => axios.ori.post('/api/login', qs.stringify(authData), {
  headers: {
    'Content-Type': 'application/x-www-form-urlencoded'
  }
}).then(data => [undefined, data]).catch(e => [e])

export const postLogout = async () => axios.ori.post('api/logout').then(data => [undefined, data.data]).catch(e => [e])

export const postLeaveImpersonation = async () => axios.ori.delete('/api/impersonate').then(data => [undefined, data.data]).catch(e => [e])

export const postForgotPassword = async (email) => axios.ori.post('api/password/email', email).then(data => [undefined, data.data]).catch(e => [e])

export const postPasswordReset = async (token, email, password, passwordConfirmation) => axios.ori.post('api/password/reset', token, email, password, passwordConfirmation).then(data => [undefined, data.data]).catch(e => [e])
