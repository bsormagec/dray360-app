export const downloadFile = (url) => {
  window.open(url, '_blank')

  // not entirely sure why this is necessary, but this is the logic for triggering a DL elsewhere in the app.
  // const link = document.createElement('a')
  // link.href = url
  // link.click()
  // link.remove()s
}
