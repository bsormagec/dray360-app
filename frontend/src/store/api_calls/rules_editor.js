import axios from '@/store/api_calls/config/axios'
import toParams from '@/utils/to_params'

export const getRules = async () => axios.get('/api/ocr/rules').then(data => [undefined, data.data]).catch(e => [e])

export const getCompanyVariantRules = async (params) => axios.get(`/api/ocr/rules-assignment?${toParams(params)}`).then(data => [undefined, data.data]).catch(e => [e])

export const putEditRule = async (ruleData) => axios.put('/api/ocr/rules/' + ruleData.id, ruleData).then(data => [undefined, data.data]).catch(e => [e])

export const saveRulesAssigment = async (rulesAssignment) => axios.post('/api/ocr/rules-assignment', rulesAssignment).then(data => [undefined, data.data]).catch(e => [e])

export const createRule = async (rule) => axios.post('/api/ocr/rules', rule).then(data => [undefined, data.data]).catch(e => [e])

// sort in alphabetical order is temporary.
// will need a better way to classify and display separately
export const getVariants = async (params = {}) => axios.get('/api/ocr/variants?sort=abbyy_variant_name', { params }).then(data => [undefined, data.data.data]).catch(e => [e])

export const testRule = async (orderId, singleCompanyVariantRule) => axios.get('/api/orders/' + orderId)
  .then(function (response) {
    let testingOutput = null

    const fetchedOcrData = response.data.ocr_data
    delete fetchedOcrData.fields_overwritten

    fetchedOcrData.original_output = fetchedOcrData.fields

    // send the rules engine the original fields
    fetchedOcrData.fields = fetchedOcrData.original_fields

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
        const retval = { output: response.data, input: fetchedOcrData, status: response.status, statusText: response.statusText }
        return retval
      })
      .catch(function (error) {
        // The request was made and the server responded with a status code
        // that falls out of the range of 2xx
        // console.log('error.response.data', error.response.data)
        console.log('error.response.status', error.response.status)
        // console.log('error.response.headers', error.response.headers)
        const errval = { input: fetchedOcrData, output: error.response.data, status: error.response.status, statusText: error.response.statusText }
        // console.log('errval: ', errval)
        // alert(error)
        return errval
      })

    return testingOutput
  })
  .catch(function (error) {
    alert(error)
  })
