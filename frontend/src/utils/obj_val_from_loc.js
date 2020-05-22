export const objValFromLocation = ({ obj, location, separator }) => {
  try {
    const parts = location.split(separator)
    let value = obj

    parts.forEach(part => {
      value = value[part]
    })

    return value
  } catch (e) {
    console.log(e)
  }
}
