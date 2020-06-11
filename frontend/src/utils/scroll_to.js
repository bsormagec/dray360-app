import testScroll from '@/utils/for_tests/scroll_handler'

export const scrollTo = (id) => {
  if (process.env.NODE_ENV === 'test') {
    testScroll.setScroll(id)
    return
  }

  try {
    document.getElementById(id).scrollIntoView()
  } catch (e) {
    return e
  }
}
