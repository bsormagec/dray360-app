export default {
  data: () => ({
    isMedium: false
  }),

  beforeMount () {
    this.setIsMedium()
    window.addEventListener('resize', this.setIsMedium)
  },

  destroyed () {
    window.removeEventListener('resize', this.setIsMedium)
  },

  methods: {
    setIsMedium () {
      this.isMedium = window.innerWidth <= 1410
    }
  }
}
