import axios from '@/store/api_calls/config/axios'

export const getLibrary = async () => axios.ext.get('/api/ocr/rules').then(data => [undefined, data.data]).catch(e => [e])

export const getAccountVariantRules = async (accountId, variantId) => axios.ext.get('/api/ocr/rules-assignment?account_id=' + accountId + '&variant_id=' + variantId).then(data => [undefined, data.data]).catch(e => [e])

export const putEditRule = async (ruleData) => axios.ext.put('/api/ocr/rules/' + ruleData.id, ruleData).then(data => [undefined, data.data]).catch(e => [e])

export const postSaveRuleSequence = async (sequenceData) => axios.ext.post('/api/ocr/rules-assignment', sequenceData).then(data => [undefined, data.data]).catch(e => [e])

export const postAddRule = async (ruleData) => axios.ext.post('/api/ocr/rules', ruleData).then(data => [undefined, data.data]).catch(e => [e])

export const getRuleCode = async (index, accountId, variantId) => axios.ext.get('/api/ocr/rules-assignment?account_id=' + accountId + '&variant_id=' + variantId, index).then(data => [undefined, data.data]).catch(e => [e])

export const getTestingOutput = async (orderId, singleAccountVariantRule) => axios.ext.get('/api/orders/' + orderId)
  .then(function (response) {
    let testingOutput = null

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

    const headers = {
      'Access-Control-Allow-Origin': '*'
    }

    testingOutput = axios.post('https://i0mgwmnrb1.execute-api.us-east-2.amazonaws.com/default/ocr-rules-engine-dev',
      fetchedOcrData,
      {
        withCredentials: false,
        headers: headers
      })
      .then(function (response) {
        console.log('response:', response.data)
        return response.data
      })
      .catch(function (error) {
        alert(error)
      })

    return testingOutput
  })
  .catch(function (error) {
    alert(error)
  })
