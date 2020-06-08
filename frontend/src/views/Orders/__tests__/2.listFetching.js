import Orders from '@/views/Orders/Orders'
import store from '@/store/modules/orders'
import mount from '@/utils/for_tests/mount_authenticated'
import sleep from '@/utils/for_tests/sleep'
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
      await sleep(300) // wait until loading animation finishes

      expect(wrapper.find('.listbody').exists()).toBe(true)
    })
  })
