import { waitFor } from '@testing-library/vue'
import renderWithVuetify from '@/utils/for_tests/render_with_vuetify'
import elNotFound from '@/utils/for_tests/el_not_found'
import authTestUser from '@/utils/for_tests/auth_test_user'
import Orders from '@/views/Orders/Orders'
import store from '@/store/modules/orders'

export default () =>
  describe('listFetching', () => {
    it('doesnt render list items before fetch', async () => {
      await authTestUser()
      const { getByTestId } = renderWithVuetify(Orders, { store })
      expect(elNotFound({ getFn: getByTestId, elTestId: 'test-list' })).toBeTruthy()
    })

    it('renders list items after fetch', async () => {
      await authTestUser()
      const { getByTestId } = renderWithVuetify(Orders, { store })
      const listTable = await waitFor(() =>
        getByTestId('test-list')
      )

      expect(listTable).toBeTruthy()
    })
  })
