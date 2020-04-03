const waitForResponseCreator = () => {
  let requesting

  const initRequest = () => (requesting = true)

  const finishRequest = () => (requesting = false)

  const wait = () => new Promise((resolve, reject) => {
    const resInterval = setInterval(() => {
      if (requesting === false) {
        clearInterval(resInterval)
        return resolve()
      }
    }, 100)
  })

  return {
    initRequest,
    finishRequest,
    wait
  }
}

const waitForResponse = waitForResponseCreator()
export default waitForResponse
