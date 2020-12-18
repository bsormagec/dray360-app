export default (objParams) => {
  const params = []

  Object.keys(objParams).forEach((key) => {
    if (!objParams[key]) return
    params.push(`${key}=${encodeURIComponent(objParams[key])}`)
  })

  return params.join('&')
}
