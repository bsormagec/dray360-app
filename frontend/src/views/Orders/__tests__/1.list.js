import listFetching from './2.listFetching'
import listColumnToggling from './3.listColumnToggling'
import listPagination from './4.listPagination'

export default () =>
  describe('list', () => {
    listFetching()
    listColumnToggling()
    listPagination()
  })
