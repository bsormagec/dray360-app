import Details from '@/views/Details/Details'
import store from '@/store/modules/orders'
import mount from '@/utils/for_tests/mount_authenticated'
import waitForResponse from '@/utils/for_tests/wait_for_response'
import sleep from '@/utils/for_tests/sleep'

export default () =>
  describe('formFieldHighlighting', () => {
    it('highlights a field on moseover and de-highlights the field on mouseleave', async () => {
      const wrapper = await mount(Details, { store })
      await waitForResponse.wait()

      /* moseover */
      let field = wrapper.find('div[test-id="shipment designation"] .form-field-highlight')
      field.element.dispatchEvent(new MouseEvent('mouseover'))
      await sleep(300) // wait for hover delay
      await wrapper.vm.$nextTick()
      let fieldClasses = wrapper.find('div[test-id="shipment designation"] .form-field-highlight').attributes('class')
      expect(fieldClasses.includes('hover')).toBe(true)
      /* -- */

      /* mouseleave */
      field = wrapper.find('div[test-id="shipment designation"] .form-field-highlight')
      field.element.dispatchEvent(new MouseEvent('mouseleave'))
      await sleep(300) // wait for hover delay
      await wrapper.vm.$nextTick()
      fieldClasses = wrapper.find('div[test-id="shipment designation"] .form-field-highlight').attributes('class')
      expect(fieldClasses.includes('hover')).toBe(false)
      /* -- */
    })
  })
