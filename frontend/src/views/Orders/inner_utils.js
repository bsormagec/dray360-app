export const listFormat = (list, cb) => list.map(item => ({
  ...formatIfEmpty(item),
  created_at: new Date(item.created_at).toLocaleTimeString([], { year: 'numeric', month: 'numeric', day: 'numeric', hour: '2-digit', minute: '2-digit' }), //  "item.created_at = intake started date" and "item.updated_at = latest status date"
  action: cb
}))

function formatIfEmpty (item) {
  const value = {}

  for (const key in item) {
    if (item[key]) {
      value[key] = item[key]
    } else {
      value[key] = '--'
    }
  }

  return value
}
