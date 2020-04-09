export default {
  data: () => ({
    isMobile: false
  }),

  beforeMount () {
    this.setIsMobile()
    window.addEventListener('resize', this.setIsMobile)
  },

  destroyed () {
    window.removeEventListener('resize', this.setIsMobile)
  },

  methods: {
    setIsMobile () {
      this.isMobile = window.innerWidth < 1024
    }
  }
}
