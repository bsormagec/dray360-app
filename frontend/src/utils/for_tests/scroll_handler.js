const ScrollHandler = () => {
  let last = ''

  const setScroll = (id) => (last = id)

  return {
    setScroll,
    getScrolled: () => last
  }
}

const scroll = ScrollHandler()
export default scroll
