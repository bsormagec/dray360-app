import Orders from '@/views/Orders/Orders'
import store from '@/store/modules/orders'
import mount from '@/utils/for_tests/mount_authenticated'
import waitForResponse from '@/utils/for_tests/wait_for_response.js'

export default () =>
  describe('listColumnToggling', () => {
    it('hides a column', async () => {
      const wrapper = await mount(Orders, { store })
      await waitForResponse.wait()

      wrapper.find('.header__dropdown div[role="button"]').trigger('click')
      await wrapper.vm.$nextTick()
      wrapper.find('[role="listbox"] div:first-child').trigger('click')
      await wrapper.vm.$nextTick()

      const firstOpText = wrapper.find('div[role="listbox"] div:first-child').text()
      const headerCell = wrapper.find(`th[aria-label="${firstOpText}"]`)
      expect(headerCell.exists()).toBe(false)
    })

    it('shows previously hidden column', async () => {
      const wrapper = await mount(Orders, { store })
      await waitForResponse.wait()

      wrapper.find('.header__dropdown div[role="button"]').trigger('click')
      await wrapper.vm.$nextTick()
      wrapper.find('div[role="listbox"] div:first-child').trigger('click')
      await wrapper.vm.$nextTick()
      /*
        Click again same option to show previously hidden column
      */
      wrapper.find('div[role="listbox"] div:first-child').trigger('click')
      await wrapper.vm.$nextTick()
      /* end */

      const firstOpText = wrapper.find('div[role="listbox"] div:first-child').text()
      const headerCell = wrapper.find(`th[aria-label="${firstOpText}"]`)
      expect(headerCell.exists()).toBe(true)
    })
  })

function sleep () {
  return new Promise((resolve, reject) => {
    setTimeout(resolve, 500)
  })
}
