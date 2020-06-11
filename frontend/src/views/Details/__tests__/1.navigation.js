import Details from '@/views/Details/Details'
import store from '@/store/modules/orders'
import mount from '@/utils/for_tests/mount_authenticated'
import waitForResponse from '@/utils/for_tests/wait_for_response'
import scrollHandler from '@/utils/for_tests/scroll_handler'

export default () =>
  describe('navigation', () => {
    it('scrolls to desired form element', async () => {
      const wrapper = await mount(Details, { store })
      await waitForResponse.wait()

      const notesStep = wrapper.find('a.notes .v-stepper__step')
      notesStep.trigger('click')
      await wrapper.vm.$nextTick()
      const stepClasses = wrapper.find('a.notes .v-stepper__step').attributes('class')
      expect(stepClasses.includes('active')).toBe(true)
      expect(scrollHandler.getScrolled()).toMatch('notes-viewing')

      const shipmentStep = wrapper.find('a.shipment .v-stepper__step')
      shipmentStep.trigger('click')
      await wrapper.vm.$nextTick()
      const shipmentClasses = wrapper.find('a.shipment .v-stepper__step').attributes('class')
      expect(shipmentClasses.includes('active')).toBe(true)
      expect(scrollHandler.getScrolled()).toMatch('shipment-viewing')
    })

    it('toggles form between view/edit', async () => {
      const wrapper = await mount(Details, { store })
      await waitForResponse.wait()

      const toggleBtn = wrapper.find('div.sidebar__body button[test-id="toggle-btn"]')

      expect(wrapper.find('div.form-viewing').isVisible()).toBe(true)
      expect(wrapper.find('div.form-editing').isVisible()).toBe(false)

      toggleBtn.trigger('click')
      await wrapper.vm.$nextTick()

      expect(wrapper.find('div.form-viewing').isVisible()).toBe(false)
      expect(wrapper.find('div.form-editing').isVisible()).toBe(true)

      toggleBtn.trigger('click')
      await wrapper.vm.$nextTick()

      expect(wrapper.find('div.form-viewing').isVisible()).toBe(true)
      expect(wrapper.find('div.form-editing').isVisible()).toBe(false)
    })
  })
