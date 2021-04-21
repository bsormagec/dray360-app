import flatten from 'lodash/flatten'

export const flatMapAudits = (audits, modelType) => {
  const auditsArray = []
  for (const key in audits) {
    auditsArray.push(audits[key])
  }

  return flatten(auditsArray).map(audit => {
    audit.model_type = modelType
    return audit
  })
}
