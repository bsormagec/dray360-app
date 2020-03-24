export const listFormat = (list) => list.map(item => ({
  ...item,
  created_at: new Date(item.created_at).toLocaleDateString()
}))
