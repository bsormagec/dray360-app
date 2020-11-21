import axios from '@/store/api_calls/config/axios'

export const getLibrary = async () => axios.get('/api/ocr/rules').then(data => [undefined, data.data]).catch(e => [e])

export const getCompanyVariantRules = async (companyId, variantId) => axios.get('/api/ocr/rules-assignment?company_id=' + companyId + '&variant_id=' + variantId).then(data => [undefined, data.data]).catch(e => [e])

export const putEditRule = async (ruleData) => axios.put('/api/ocr/rules/' + ruleData.id, ruleData).then(data => [undefined, data.data]).catch(e => [e])

export const postSaveRuleSequence = async (sequenceData) => axios.post('/api/ocr/rules-assignment', sequenceData).then(data => [undefined, data.data]).catch(e => [e])

export const postAddRule = async (ruleData) => axios.post('/api/ocr/rules', ruleData).then(data => [undefined, data.data]).catch(e => [e])

export const getRuleCode = async (index, companyId, variantId) => axios.get('/api/ocr/rules-assignment?company_id=' + companyId + '&variant_id=' + variantId, index).then(data => [undefined, data.data]).catch(e => [e])

export const getCompanyList = async () => axios.get('/api/companies').then(data => [undefined, data.data.data]).catch(e => [e])

export const getVariantList = async (params = {}) => axios.get('/api/ocr/variants', { params }).then(data => [undefined, data.data.data]).catch(e => [e])

export const getTestingOutput = async (orderId, singleCompanyVariantRule) => axios.get('/api/orders/' + orderId)
  .then(function (response) {
    let testingOutput = null

    const fetchedOcrData = response.data.ocr_data
    delete fetchedOcrData.fields_overwritten

    // fetchedOcrData.rules = vc.company_variant_rules[index] -=> singleCompanyVariantRule
    fetchedOcrData.rules = singleCompanyVariantRule

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

    testingOutput = axios.post('https://b9da68xgc1.execute-api.us-east-2.amazonaws.com/api/1.0/dev/ocrrules-engine',
      fetchedOcrData,
      {
        withCredentials: false,
        headers: headers
      })
      .then(function (response) {
        // console.log('testingOutput response:', response.data)
        const retval = { output: response.data, input: fetchedOcrData }
        return retval
      })
      .catch(function (error) {
        if (error.response) {
          // The request was made and the server responded with a status code
          // that falls out of the range of 2xx
          console.log('error.response.data', error.response.data)
          console.log('error.response.status', error.response.status)
          console.log('error.response.headers', error.response.headers)
        }
        alert(error)
        return error
      })

    return testingOutput
  })
  .catch(function (error) {
    alert(error)
  })
