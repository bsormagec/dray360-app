import { statuses } from '@/enums/app_objects_types'

export const requestMatchesFilters = (request, filters) => {
  const matches = []

  filters.forEach(filter => {
    if (filter.type === 'status') {
      matches.push(filter.value.includes(request.display_status))
    } else if (filter.type === 'system_status') {
      matches.push(filter.value.includes(request.status))
    } else if (filter.type === 'company_id') {
      matches.push(filter.value.includes(request.company_id))
    } else {
      matches.push(true)
    }
  })

  const displayHidden = filters.findIndex(filter => filter.type === 'displayHidden') !== -1
  matches.push(!(request.status === statuses.requestMarkedDone && !displayHidden))

  return matches.reduce((a, b) => a + b, 0) === (filters.length + 1)
}
