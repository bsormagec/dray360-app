import { waitFor } from '@testing-library/vue'
import renderAuthenticated from '@/utils/for_tests/render_authenticated'
import elNotFound from '@/utils/for_tests/el_not_found'
import Orders from '@/views/Orders/Orders'
import store from '@/store/modules/orders'

export default () =>
  describe('listFetching', () => {
    it('doesnt render list items before fetch', async () => {
      const { getByTestId } = await renderAuthenticated(Orders, { store })
      expect(elNotFound({ getFn: getByTestId, elTestId: 'test-list' })).toBeTruthy()
    })

    it('renders list items after fetch', async () => {
      const { getByTestId } = await renderAuthenticated(Orders, { store })
      const listTable = await waitFor(() =>
        getByTestId('test-list')
      )

      expect(listTable).toBeTruthy()
    })
  })
