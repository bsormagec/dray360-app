<template>
  <div class="footer">
    <span class="footer__indicator">{{ indicatorText }}</span>

    <div class="footer__jump">
      <span>Jump to page</span>
      <v-text-field
        label="#"
        solo
        dense
        hide-details
        @change="jumpToPage"
      />
    </div>

    <div class="footer__navigation">
      <div
        v-for="btn in navigationButtons"
        :key="btn.value"
        :class="`navigation__btn ${btn.type}`"
      >
        <v-btn
          class="btn__single"
          color="primary"
          :outlined="isOutlined(btn)"
          @click="btn.action(btn.value + 1)"
        >
          {{ typeof btn.value === 'number' ? btn.value + 1 : btn.value }}
        </v-btn>
      </div>
    </div>
  </div>
</template>

<script>
import arrayFromNumber from '@/utils/arrayFromNumber'
import { providerStateName } from '@/views/Orders/inner_types'

const mockLastPage = 10 // only for testing

export default {
  name: 'OrdersListFooter',

  inject: [providerStateName],

  props: {
    activePage: {
      type: Number,
      required: true
    },

    setActivePage: {
      type: Function,
      required: true
    }
  },

  data () {
    const { meta } = this[providerStateName]

    return {
      meta,
      cut: 4,
      showingSlice: 0
    }
  },

  computed: {
    pagesArray () {
      const { last_page: lastPage } = this.meta()
      return arrayFromNumber({ length: mockLastPage || lastPage })
    },

    allNBtns () {
      return this.pagesArray.map((n, i) => ({
        type: 'numberedButton',
        value: n,
        action: this.setActivePage
      }))
    },

    slicedNBtns () {
      // Array of arrays
      // E.g. [[1,2,3,4],[5,6,7,8],[9,10]]
      let count = 0
      let current = []
      const sliced = []

      this.allNBtns.forEach((btn, i) => {
        count++
        current.push(btn)
        if (count >= this.cut) {
          count = 0
          sliced.push(current)
          current = []
        }
      })

      if (sliced.length) sliced.push(current)
      return sliced
    },

    navigationButtons () {
      return [
        {
          type: 'first',
          value: 'First',
          action: this.firstBtn
        },
        {
          type: 'prev',
          value: 'Prev',
          action: this.prevBtn
        },
        ...this.slicedNBtns[this.showingSlice],
        {
          type: 'next',
          value: 'Next',
          action: this.nextBtn
        },
        {
          type: 'last',
          value: 'Last',
          action: this.lastBtn
        }
      ]
    },

    indicatorText () {
      const { from, to, total } = this.meta()
      const notLoaded = !from || !to || !total

      if (notLoaded) return '--'
      return `${from} - ${to} of ${total}`
    }
  },

  methods: {
    async jumpToPage (n) {
      const page = Number(n)
      if (isNaN(page) || page <= 0 || page === this.activePage || page > this.pagesArray.length) return

      let index
      this.slicedNBtns.forEach((slice, i) => {
        if (slice.find(btn => btn.value + 1 === page)) {
          index = i
        }
      })

      this.showingSlice = index
      await this.setActivePage(page)
    },

    async firstBtn () {
      this.showingSlice = 0
      await this.setActivePage(1)
    },

    async prevBtn () {
      if (!this.slicedNBtns[this.showingSlice - 1]) return
      this.showingSlice -= 1
      this.setActivePage(this.slicedNBtns[this.showingSlice][0].value + 1)
    },

    async nextBtn () {
      if (!this.slicedNBtns[this.showingSlice + 1]) return
      this.showingSlice += 1
      this.setActivePage(this.slicedNBtns[this.showingSlice][0].value + 1)
    },

    async lastBtn () {
      const lastSliceIndex = this.slicedNBtns.length - 1
      const lastSlice = this.slicedNBtns[lastSliceIndex]
      const lastButton = lastSlice[this.slicedNBtns[lastSliceIndex].length - 1]
      this.showingSlice = lastSliceIndex
      await this.setActivePage(lastButton.value + 1)
    },

    isOutlined ({ value }) {
      return value + 1 !== this.activePage
    }
  }
}
</script>

<style lang="scss" scoped>
.footer {
  display: flex;
  align-items: center;
  margin-top: 2.4rem;
}

.footer__indicator {
  margin-right: auto;
  font-size: 1.44rem !important;
  font-weight: bold;
}

.footer__jump {
  display: flex;
  align-items: center;
  width: 14rem;
  margin-right: 2.1rem;

  span {
    min-width: 8rem;
    font-size: 1.3rem !important;
    font-weight: bold;
    margin-right: 0.8rem;
  }
}

.footer__navigation {
  display: flex;

  // .navigation__btn {
  //   &.numberedButton:not(:first-child) .btn__single,
  //   &.numberedButton:not(:last-child) .btn__single {
  //     border-radius: unset;
  //   }

  //   &.numberedButton-last .btn__single {
  //     border-top-right-radius: 0.4rem !important;
  //     border-bottom-right-radius: 0.4rem !important;
  //   }

  //   &.numberedButton-first .btn__single {
  //     border-top-left-radius: 0.4rem !important;
  //     border-bottom-left-radius: 0.4rem !important;
  //   }
  // }
}
</style>
