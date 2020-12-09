import axios from '@/store/api_calls/config/axios'
import toParams from '@/utils/to_params'

export const getUser = async (id) => axios.get(`/api/users/${id}`).then(data => [undefined, data.data]).catch(e => [e])

export const getUsers = async (filters, query) => axios.get(`/api/users?${toParams(filters)}`).then(data => [undefined, data.data]).catch(e => [e])

export const deleteUser = async (id) => axios.delete(`/api/users/${id}`).then(data => [undefined, data.data]).catch(e => [e])

export const editUser = async (userData, id) => axios.put(`/api/users/${id}`, userData).then(data => [undefined, data.data]).catch(e => [e])

export const addUser = async (userData) => axios.post('/api/users', userData).then(data => [undefined, data.data]).catch(e => [e])

export const getRoles = async () => axios.get('/api/roles').then(data => [undefined, data.data]).catch(e => [e])

export const changeUserStatus = async (id, newStatus) => axios.put(`/api/users/${id}/status`, newStatus).then(data => [undefined, data.data]).catch(e => [e])
