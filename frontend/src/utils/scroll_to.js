export const scrollTo = (id) => {
  try {
    document.getElementById(id).scrollIntoView()
  } catch (e) {
    return e
  }
}
