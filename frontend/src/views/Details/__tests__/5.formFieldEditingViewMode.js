import Details from '@/views/Details/Details'
import store from '@/store/modules/orders'
import mount from '@/utils/for_tests/mount_authenticated'
import waitForResponse from '@/utils/for_tests/wait_for_response'
import scrollHandler from '@/utils/for_tests/scroll_handler'
import sleep from '@/utils/for_tests/sleep'

export default () =>
  describe('formFieldEditingViewMode', () => {
    it('selects a block in the document', async () => {
      const wrapper = await mount(Details, { store })
      await waitForResponse.wait()

      wrapper.find('#tem1-viewing .form-field-highlight').trigger('click')
      await sleep(300) // wait for hover delay
      await wrapper.vm.$nextTick()

      expect(scrollHandler.getScrolled()).toMatch('tem1-document')
    })

    it('updates field type input', async () => {
      const wrapper = await mount(Details, { store })
      await waitForResponse.wait()

      const newValue = 'test'

      wrapper.find('#shipmentdesignation-viewing .form-field-highlight').trigger('click')
      const input = wrapper.find('#shipmentdesignation-viewing .form-field-highlight input')
      input.setValue(newValue)
      input.trigger('keydown.enter')
      await wrapper.vm.$nextTick()

      wrapper.find('#shipmentdesignation-viewing .form-field-highlight .btns__accept').trigger('click')
      await wrapper.vm.$nextTick()

      const value = wrapper.find('#shipmentdesignation-viewing .form-field-highlight .field__value').text()
      expect(value).toMatch(newValue)
    })

    // it('updates field type text-area', async () => {
    //   const wrapper = await mount(Details, { store })
    //   await waitForResponse.wait()

    //   const newValue = 'test-2'
    //   wrapper.find('#shipmentdirection-viewing .form-field-highlight').trigger('click')
    //   const textarea = wrapper.find('#shipmentdirection-viewing .form-field-highlight .form-field-element-textarea')
    //   textarea.setProps({})
    //   await wrapper.vm.$nextTick()

    //   wrapper.find('#shipmentdirection-viewing .form-field-highlight .btns__accept').trigger('click')
    //   await wrapper.vm.$nextTick()

    //   const value = wrapper.find('#shipmentdirection-viewing .form-field-highlight .field__value').text()
    //   expect(value).toMatch(newValue)
    // })

    it('updates field type select', async () => {
      const wrapper = await mount(Details, { store })
      await waitForResponse.wait()

      wrapper.find('#shipmenthandling-viewing .form-field-element-select div[role="button"]').trigger('click')
      await wrapper.vm.$nextTick()
      const firstOption = wrapper.findAll('div[tabindex="0"]').at(0)
      firstOption.trigger('click')
      await wrapper.vm.$nextTick()

      wrapper.find('#shipmenthandling-viewing .form-field-highlight .btns__accept').trigger('click')
      await wrapper.vm.$nextTick()

      const valueText = wrapper.find('#shipmenthandling-viewing .form-field-highlight .field__value').text()
      expect(valueText).toMatch(firstOption.text())
    })

    it('updates field type switch', async () => {
      const wrapper = await mount(Details, { store })
      await waitForResponse.wait()

      const initialValueText = wrapper.find('#oneway-viewing .form-field-highlight .field__value').text()

      wrapper.find('#oneway-viewing .form-field-highlight').trigger('click')
      const switchInput = wrapper.find('#oneway-viewing .form-field-highlight input')
      await switchInput.setChecked(true)

      wrapper.find('#oneway-viewing .form-field-highlight .btns__accept').trigger('click')
      await wrapper.vm.$nextTick()

      const valueText = wrapper.find('#oneway-viewing .form-field-highlight .field__value').text()
      expect(initialValueText).not.toMatch(valueText)
    })

    it('updates field type date-time', async () => {
      const wrapper = await mount(Details, { store })
      await waitForResponse.wait()

      wrapper.find('#estarrival-viewing .form-field-highlight').trigger('click')
      wrapper.find('#estarrival-viewing .form-field-highlight .datetime__date input').trigger('click')
      await wrapper.vm.$nextTick()

      const tds = wrapper.findAll('div.v-dialog__content.v-dialog__content--active td')
      const lastTd = tds.at(tds.length - 1)
      lastTd.find('button').trigger('click')
      await wrapper.vm.$nextTick()

      const timeValue = '2pm'
      const timeInput = wrapper.find('#estarrival-viewing .form-field-highlight .form-field-element-time input')
      timeInput.setValue(timeValue)
      timeInput.trigger('blur')
      await wrapper.vm.$nextTick()

      wrapper.find('#estarrival-viewing .form-field-highlight .btns__accept').trigger('click')
      await wrapper.vm.$nextTick()

      const fieldValue = wrapper.find('#estarrival-viewing .form-field-highlight').text()
      expect(fieldValue.includes(lastTd.text()) && fieldValue.includes(timeValue)).toBe(true)
    })
  })
