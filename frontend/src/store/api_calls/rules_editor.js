import axios from '@/store/api_calls/config/axios'

export const getLibrary = async () => axios.get('/api/ocr/rules').then(data => [undefined, data.data]).catch(e => [e])

export const getAccountVariantRules = async () => axios.get('/api/ocr/rules-assignment?account_id=8&variant_id=8').then(data => [undefined, data.data]).catch(e => [e])

export const putEditRule = async (ruleData) => axios.put('/api/ocr/rules/' + ruleData.id, ruleData).then(data => [undefined, data.data]).catch(e => [e])

export const postSaveRuleSequence = async (sequenceData) => axios.post('/api/ocr/rules-assignment', sequenceData).then(data => [undefined, data.data]).catch(e => [e])

export const postAddRule = async (ruleData) => axios.post('/api/ocr/rules', ruleData).then(data => [undefined, data.data]).catch(e => [e])

export const getRuleCode = async (index) => axios.get('/api/ocr/rules-assignment?account_id=8&variant_id=8', index).then(data => [undefined, data.data]).catch(e => [e])

export const getTestingOutput = async (orderId, singleAccountVariantRule) => axios.get('/api/orders/' + orderId)
  .then(function (response) {
    const fetchedOcrData = response.data.ocr_data
    delete fetchedOcrData.fields_overwritten

    // fetchedOcrData.rules = vc.account_variant_rules[index] -=> singleAccountVariantRule
    fetchedOcrData.rules = singleAccountVariantRule

    const testedRuleName = fetchedOcrData.rules.name
    const testedRuleCode = fetchedOcrData.rules.code

    fetchedOcrData.rules = []
    fetchedOcrData.rules.push({
      [testedRuleName]: testedRuleCode
    })
    fetchedOcrData[testedRuleName] = testedRuleCode

    // console.log('fetchedOcrData[testedRuleName]', fetchedOcrData[testedRuleName])

    axios.post('https://i0mgwmnrb1.execute-api.us-east-2.amazonaws.com/default/ocr-rules-engine-dev',
      fetchedOcrData)
      .then(function (response) {
        // vc.testing_output = response.data
        console.log('return response.data: ', response.data)
        return [undefined, response.data]
      })
      .catch(function (error) {
        alert(error)
      })
  })
  .catch(function (error) {
    alert(error)
  })
