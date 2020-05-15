export const listFormat = (list, cb) => list.map(item => ({
  ...item,
  created_at: new Date(item.created_at).toLocaleDateString(),
  action: cb
}))
