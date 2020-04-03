import Orders from '@/views/Orders/Orders'
import store from '@/store/modules/orders'
import mount from '@/utils/for_tests/mount_authenticated'
import waitForResponse from '@/utils/for_tests/wait_for_response.js'

let storedTest

export default () =>
  describe('listPagination', () => {
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
    })

    it('changes list when clicking numbered buttons', async () => {
      const wrapper = await mount(Orders, { store })
      await waitForResponse.wait()

      const prevFirst = wrapper.findAll('tbody tr').at(0)
      const prevFirstText = prevFirst.text()

      const numberedButtons = wrapper.find('.footer__navigation').findAll('.buttons').at(1)
      const secondBtn = numberedButtons.findAll('.buttons__single').at(1)
      secondBtn.find('button').trigger('click')
      await waitForResponse.wait()

      const afterFirst = wrapper.findAll('tbody tr').at(0)
      const afterFirstText = afterFirst.text()

      expect(prevFirstText).not.toMatch(afterFirstText)
    })

    it('changes list when clicking "next" button', async () => {
      const wrapper = await mount(Orders, { store })
      await waitForResponse.wait()

      const prevFirst = wrapper.findAll('tbody tr').at(0)
      const prevFirstText = prevFirst.text()

      const lastBtns = wrapper.find('.footer__navigation').findAll('.buttons').at(2)
      const nextBtn = lastBtns.findAll('.buttons__single').at(0)
      nextBtn.find('button').trigger('click')
      await waitForResponse.wait()

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

        const lastBtns = wrapper.find('.footer__navigation').findAll('.buttons').at(2)
        const lastBtn = lastBtns.findAll('.buttons__single').at(1)
        lastBtn.find('button').trigger('click')
        await waitForResponse.wait()

        const afterFirst = wrapper.findAll('tbody tr').at(0)
        const afterFirstText = afterFirst.text()

        expect(prevFirstText).not.toMatch(afterFirstText)
        return wrapper
      }

      await storedTest()
    })

    it('changes list when clicking "prev" button', async () => {
      const wrapper = await storedTest()

      const prevFirst = wrapper.findAll('tbody tr').at(0)
      const prevFirstText = prevFirst.text()

      const firstBtns = wrapper.find('.footer__navigation').findAll('.buttons').at(0)
      const prevBtn = firstBtns.findAll('.buttons__single').at(1)
      prevBtn.find('button').trigger('click')
      await waitForResponse.wait()

      const afterFirst = wrapper.findAll('tbody tr').at(0)
      const afterFirstText = afterFirst.text()

      expect(prevFirstText).not.toMatch(afterFirstText)
    })

    it('changes list when clicking "first" button', async () => {
      const wrapper = await storedTest()

      const prevFirst = wrapper.findAll('tbody tr').at(0)
      const prevFirstText = prevFirst.text()

      const firstBtns = wrapper.find('.footer__navigation').findAll('.buttons').at(0)
      const firstBtn = firstBtns.findAll('.buttons__single').at(0)
      firstBtn.find('button').trigger('click')
      await waitForResponse.wait()

      const afterFirst = wrapper.findAll('tbody tr').at(0)
      const afterFirstText = afterFirst.text()

      expect(prevFirstText).not.toMatch(afterFirstText)
    })
  })
