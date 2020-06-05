import Orders from '@/views/Orders/Orders'
import store from '@/store/modules/orders'
import mount from '@/utils/for_tests/mount_authenticated'
import waitForResponse from '@/utils/for_tests/wait_for_response'

let storedTest

export default () =>
  describe('listPagination', () => {
    it('doesnt change the list when clicking "first" button if its disabled', async () => {
      const wrapper = await mount(Orders, { store })
      await waitForResponse.wait()

      const FirstBtn = wrapper.find('.First button')

      expect(FirstBtn.attributes('disabled')).toBe('disabled')
      const prevFirstText = wrapper.findAll('tbody tr').at(0).text()

      FirstBtn.trigger('click')
      await waitForResponse.wait()
      await wrapper.vm.$nextTick()

      const afterFirstText = wrapper.findAll('tbody tr').at(0).text()
      expect(prevFirstText).toMatch(afterFirstText)
      wrapper.destroy()
    })

    it('changes list when using "jump to page" field', async () => {
      const wrapper = await mount(Orders, { store })
      await waitForResponse.wait()

      const prevFirst = wrapper.findAll('tbody tr').at(0)
      const prevFirstText = prevFirst.text()

      const jumpInput = wrapper.find('.footer__jump .v-input input')
      jumpInput.setValue('2')
      jumpInput.trigger('keydown.enter')
      await waitForResponse.wait()

      const afterFirst = wrapper.findAll('tbody tr').at(0)
      const afterFirstText = afterFirst.text()

      expect(prevFirstText).not.toMatch(afterFirstText)
      wrapper.destroy()
    })

    it('changes list when clicking numbered buttons', async () => {
      const wrapper = await mount(Orders, { store })
      await waitForResponse.wait()

      const prevFirst = wrapper.findAll('tbody tr').at(0)
      const prevFirstText = prevFirst.text()

      const Btn = wrapper.findAll('.footer__navigation .buttons').at(0).find('.buttons__single button')
      Btn.trigger('click')
      await waitForResponse.wait()
      await wrapper.vm.$nextTick()

      const afterFirst = wrapper.findAll('tbody tr').at(0)
      const afterFirstText = afterFirst.text()

      expect(prevFirstText).not.toMatch(afterFirstText)
    })

    it('changes list when clicking "last" button', async () => {
      storedTest = async () => {
        const wrapper = await mount(Orders, { store })
        await waitForResponse.wait()

        const prevFirst = wrapper.findAll('tbody tr').at(0)
        const prevFirstText = prevFirst.text()

        const lastButton = wrapper.findAll('.footer__navigation .buttons').at(2).find('.buttons__single button')

        lastButton.trigger('click')
        await waitForResponse.wait()

        const afterFirst = wrapper.findAll('tbody tr').at(0)
        const afterFirstText = afterFirst.text()

        expect(prevFirstText).not.toMatch(afterFirstText)
        return wrapper
      }

      await storedTest()
    })
  })
