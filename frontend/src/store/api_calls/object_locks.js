import axios from '@/store/api_calls/config/axios'

export const postLockObject = async (data) => axios.post('/api/object-locks', data).then(data => [undefined, data.data]).catch(e => [e])

export const putRefreshLock = async (data) => axios.put('/api/object-locks', data).then(data => [undefined, data.data]).catch(e => [e])

export const deleteReleaseLock = async (data) => axios.post('/api/object-locks', { ...data, _method: 'DELETE' }).then(data => [undefined, data.data]).catch(e => [e])
