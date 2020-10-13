import qs from 'query-string'
import axios from '@/store/api_calls/config/axios'

export const getCsrfCookie = async () => axios.get('/sanctum/csrf-cookie').then(data => [undefined, data]).catch(e => [e])

export const getUser = async () => axios.get('/api/user').then(data => [undefined, data.data]).catch(e => [e])

export const postSignUp = async (signUpData) => axios.post('/api/signup', signUpData).then(data => [undefined, data]).catch(e => [e])

export const postLogin = async (authData) => axios.post('/api/login', qs.stringify(authData), {
  headers: {
    'Content-Type': 'application/x-www-form-urlencoded'
  }
}).then(data => [undefined, data]).catch(e => [e])

export const postLogout = async () => axios.post('api/logout').then(data => [undefined, data.data]).catch(e => [e])

export const postLeaveImpersonation = async () => axios.delete('/api/impersonate').then(data => [undefined, data.data]).catch(e => [e])

export const postForgotPassword = async (email) => axios.post('api/password/email', email).then(data => [undefined, data.data]).catch(e => [e])

export const postPasswordReset = async (token, email, password, passwordConfirmation) => axios.post('api/password/reset', token, email, password, passwordConfirmation).then(data => [undefined, data.data]).catch(e => [e])
