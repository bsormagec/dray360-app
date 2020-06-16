import axios from '@/store/api_calls/config/axios'

export const getLibrary = async () => axios.get('/api/ocr/rules').then(data => [undefined, data.data]).catch(e => [e])

export const getAccountVariantRules = async () => axios.get('/api/ocr/rules-assignment?account_id=8&variant_id=8').then(data => [undefined, data.data]).catch(e => [e])

export const putEditRule = async (ruleData) => axios.put('/api/ocr/rules/' + ruleData.id, ruleData).then(data => [undefined, data.data]).catch(e => [e])

export const postSaveRuleSequence = async (sequenceData) => axios.post('/api/ocr/rules-assignment', sequenceData).then(data => [undefined, data.data]).catch(e => [e])

export const postAddRule = async (ruleData) => axios.post('/api/ocr/rules', ruleData).then(data => [undefined, data.data]).catch(e => [e])

export const getRuleCode = async (index) => axios.get('/api/ocr/rules-assignment?account_id=8&variant_id=8', index).then(data => [undefined, data.data]).catch(e => [e])
