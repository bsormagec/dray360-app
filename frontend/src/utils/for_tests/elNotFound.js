export default ({ getFn, elTestId }) => {
  let notFound = false
  try {
    getFn(elTestId)
  } catch (e) {
    notFound = true
  }
  return notFound
}
