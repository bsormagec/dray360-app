export const listFormat = (list, cb) => list.map(item => ({
  ...formatIfEmpty(item),
  created_at: new Date(item.created_at).toLocaleDateString(),
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
