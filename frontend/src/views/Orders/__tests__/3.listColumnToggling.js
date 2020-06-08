import Orders from '@/views/Orders/Orders'
import store from '@/store/modules/orders'
import mount from '@/utils/for_tests/mount_authenticated'
import waitForResponse from '@/utils/for_tests/wait_for_response.js'

let storedTest
export default () =>
  describe('listColumnToggling', () => {
    it('hides a column', async () => {
      storedTest = async () => {
        const wrapper = await mount(Orders, { store })
        await waitForResponse.wait()

        const columnsBtn = wrapper.find('div.Columns div[role="button"]')
        columnsBtn.trigger('click')
        await wrapper.vm.$nextTick()

        const options = wrapper.findAll('div[tabindex="0"]')
        const idOption = options.at(0)
        idOption.trigger('click')
        await wrapper.vm.$nextTick()

        const idHeader = wrapper.find('th[aria-label="Id"]')
        expect(idHeader.exists()).toBe(false)
        return { wrapper, idOption }
      }
      await storedTest()
    })

    it('shows previously hidden column', async () => {
      /* hide a column again */
      const { wrapper, idOption } = await storedTest()
      /* -- */

      idOption.trigger('click')
      await wrapper.vm.$nextTick()
      const idHeader = wrapper.find('th[aria-label="Id"]')
      expect(idHeader.exists()).toBe(true)
    })
  })
