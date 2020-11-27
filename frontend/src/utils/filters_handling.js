export const getRequestFilters = (filters, filterKeyMap) => {
  return filters.reduce((o, element) => ({ ...o, [filterKeyMap[element.type]]: Array.isArray(element.value) ? element.value.join(',') : element.value }), {})
}
