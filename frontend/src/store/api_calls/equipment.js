import axios from '@/store/api_calls/config/axios'
import toParams from '@/utils/to_params'

export const getEquipmentTypes = async (companyId, tmsProviderId, filters = {}) => axios.ext.get(`/api/companies/${companyId}/tms-provider/${tmsProviderId}/equipment-types?${toParams(filters)}`).then(data => [undefined, data]).catch(e => [e])

export const getEquipmentTypeOptions = async (companyId, tmsProviderId) => axios.ext.get(`/api/companies/${companyId}/tms-provider/${tmsProviderId}/equipment-types-options`).then(data => [undefined, data]).catch(e => [e])
