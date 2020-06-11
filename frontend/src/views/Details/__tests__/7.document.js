import Details from '@/views/Details/Details'
import store from '@/store/modules/orders'
import mount from '@/utils/for_tests/mount_authenticated'
import waitForResponse from '@/utils/for_tests/wait_for_response'
import scrollHandler from '@/utils/for_tests/scroll_handler'
import sleep from '@/utils/for_tests/sleep'

export default () =>
  describe('document', () => {
    it('highlights a block', async () => {
      const wrapper = await mount(Details, { store })
      await waitForResponse.wait()

      const block = wrapper.find('#billto-document')
      block.element.dispatchEvent(new MouseEvent('mouseover'))
      await sleep(300) // wait for hover delay
      await wrapper.vm.$nextTick()

      const blockClasses = wrapper.find('#billto-document').attributes('class')
      expect(blockClasses.includes('hover')).toBe(true)
    })

    it('selects a block', async () => {
      const wrapper = await mount(Details, { store })
      await waitForResponse.wait()

      wrapper.find('#billto-document').trigger('click')
      await wrapper.vm.$nextTick()

      const blockClasses = wrapper.find('#billto-document').attributes('class')
      expect(blockClasses.includes('edit')).toBe(true)
    })

    it('selects a form field', async () => {
      const wrapper = await mount(Details, { store })
      await waitForResponse.wait()

      wrapper.find('#billto-document').trigger('click')
      await wrapper.vm.$nextTick()

      const fieldClasses = wrapper.find('#billto-viewing div.form-field-highlight').classes()
      expect(fieldClasses.includes('edit')).toBe(true)
      expect(scrollHandler.getScrolled()).toMatch('billto-viewing')
    })
  })
