import axios from '@/store/api_calls/config/axios'

export const getUsers = async () => axios.ext.get('/api/users').then(data => [undefined, data.data]).catch(e => [e])

export const deleteUser = async (id) => axios.ext.delete(`/api/users/${id}`).then(data => [undefined, data.data]).catch(e => [e])

export const editUser = async (userData, id) => axios.ext.put(`/api/users/${id}`, userData).then(data => [undefined, data.data]).catch(e => [e])

export const addUser = async (userData) => axios.ext.post('/api/users', userData).then(data => [undefined, data.data]).catch(e => [e])

export const changePassword = async (oldPassword, password, passwordConfirmation) => axios.ext.post('/api/password/change', oldPassword, password, passwordConfirmation).then(data => [undefined, data.data]).catch(e => [e])

export const getRoles = async () => axios.ext.get('/api/roles').then(data => [undefined, data.data]).catch(e => [e])

export const changeUserStatus = async (id, newStatus) => axios.ext.put(`/api/users/${id}/status`, newStatus).then(data => [undefined, data.data]).catch(e => [e])
