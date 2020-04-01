import Orders from '@/views/Orders/Orders'
import store from '@/store/modules/orders'
import mount from '@/utils/for_tests/mount_authenticated'
import waitForResponse from '@/utils/for_tests/wait_for_response.js'

export default () =>
  describe('listFetching', () => {
    it('doesnt render list items before fetch', async () => {
      const wrapper = await mount(Orders, { store })
      expect(wrapper.find('.listbody').exists()).toBe(false)
    })

    it('renders list items after fetch', async () => {
      const wrapper = await mount(Orders, { store })
      await waitForResponse.wait()
      expect(wrapper.find('.listbody').exists()).toBe(true)
    })
  })
