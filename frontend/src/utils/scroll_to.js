export const scrollTo = (elementId, wrapperSelector, offset) => {
  try {
    const topPos = document.getElementById(elementId).offsetTop
    document.querySelector(wrapperSelector || 'body').scrollTop = topPos - offset || 0
  } catch (e) {
    return e
  }
}
