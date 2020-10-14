<template>
  <span class="root">{{ text }}</span>
</template>
<script>

// import DateRangeCalendar from '@/components/Orders/DateRangeCalendar'
export default {
  name: 'Chip',
  components: {
    // DateRangeCalendar
  },
  props: {
    color: {
      type: String,
      required: false,
      default: 'blue'
    },
    text: {
      type: String,
      required: false,
      default: ''
    },
    size: {
      type: String,
      required: false,
      default: 'medium'
    },
    closeable: {
      type: Boolean,
      required: false,
      default: false
    }
  },
  data () {
    return {
      dialog: false,
      page: 1,
      headers: [],
      search: '',
      dateRange: [],
      selected: [],
      selectedHeaders: [],
      itemsPerPageArray: [4, 8, 12],
      itemsPerPage: 4,
      filter: {},
      sortDesc: false
    }
  },
  computed: {
    // showHeaders () {
    //   return this.headers.filter(s => this.selectedHeaders.includes(s))
    // }
  },

  created () {
    console.log('chip created')
    // this.headers = Object.values(this.customheaders)
    // this.selectedHeaders = this.headers
    // this.initialize()

    // if (window.location.search.includes('searchQuery')) {
    //   const urlSearchQuery = new URLSearchParams(window.location.search).get('searchQuery')
    //   this.search = urlSearchQuery
    // }

    // if (window.location.search.includes('createdBetween')) {
    //   const urlDateRange = new URLSearchParams(window.location.search).get('createdBetween')
    //   this.dateRange = urlDateRange
    // }
  },

  methods: {
    initialize () {
      console.log('yo')
    }
  }
}
</script>

<style lang="scss" scoped>
    .root {
        font-size: rem(12);
        border: black solid 1px;
        border-radius: rem(60);
        padding: rem(5) 0px 0px rem(12);
    }
</style>
