import OrdersCreate from '@/views/Orders/OrdersCreate'
import mount from '@/utils/for_tests/mount_authenticated'

const oneName = 'test1.pdf'
const secondName = 'test2.pdf'
const oneFile = new File([new ArrayBuffer()], oneName)
const twoFile = new File([new ArrayBuffer()], secondName)

export default () =>
  describe('create', () => {
    it('Uploads single file', async () => {
      const wrapper = await mount(OrdersCreate, {}, false)

      wrapper.vm.addFiles([oneFile])
      expect(wrapper.vm.files[0].name).toMatch(oneName)
    })

    it('Uploads multiple files', async () => {
      const wrapper = await mount(OrdersCreate, {}, false)

      wrapper.vm.addFiles([oneFile, twoFile])
      expect(wrapper.vm.files[0].name).toMatch(oneName)
      expect(wrapper.vm.files[1].name).toMatch(secondName)
    })

    it('Only uploads .pdf files', async () => {
      const wrapper = await mount(OrdersCreate, {}, false)

      const validFiles = [oneFile, twoFile]
      wrapper.vm.addFiles([
        ...validFiles,
        new File([new ArrayBuffer()], 'image.png'),
        new File([new ArrayBuffer()], 'excel.xls'),
        new File([new ArrayBuffer()], 'audio.mp3')
      ])

      expect(wrapper.vm.files.length).toBe(2)
      expect(wrapper.vm.files[0].name).toMatch(oneName)
      expect(wrapper.vm.files[1].name).toMatch(secondName)
    })

    it('Deletes one file', async () => {
      const wrapper = await mount(OrdersCreate, {}, false)

      wrapper.vm.addFiles([oneFile, twoFile])
      wrapper.vm.deleteFile(oneFile)

      expect(wrapper.vm.files.length).toBe(1)
      expect(wrapper.vm.files[0].name).toMatch(secondName)
    })

    it('Deletes all files', async () => {
      const wrapper = await mount(OrdersCreate, {}, false)

      wrapper.vm.addFiles([oneFile, twoFile])
      wrapper.vm.deleteAll()

      expect(wrapper.vm.files.length).toBe(0)
    })
  })
