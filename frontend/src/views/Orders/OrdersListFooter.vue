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

    <div
      class="footer__navigation"
    >
      <OrdersListFooterButtons
        :active-page="activePage"
        :buttons-list="navigationButtons.left"
      />

      <OrdersListFooterButtons
        alt-style
        :active-page="activePage"
        :buttons-list="navigationButtons.center"
      />

      <OrdersListFooterButtons
        :active-page="activePage"
        :buttons-list="navigationButtons.right"
      />
    </div>
  </div>
</template>

<script>
import arrayFromNumber from '@/utils/arrayFromNumber'
import { providerStateName } from '@/views/Orders/inner_types'

import OrdersListFooterButtons from '@/views/Orders/OrdersListFooterButtons'

export default {
  name: 'OrdersListFooter',

  inject: [providerStateName],

  components: {
    OrdersListFooterButtons
  },

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
      cut: 3,
      showingSlice: 0
    }
  },

  computed: {
    pagesArray () {
      return arrayFromNumber({ length: this.meta().last_page, from: 1 })
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
      let count = 1
      let current = []
      const sliced = []

      this.allNBtns.forEach((btn, i) => {
        count++
        current.push(btn)
        if (count > this.cut) {
          count = 1
          sliced.push(current)
          current = []
        }
      })

      if (current.length) sliced.push(current)
      return sliced
    },

    navigationButtons () {
      return {
        left: [
          {
            value: 'First',
            action: this.firstBtn
          },
          {
            value: 'Prev',
            action: this.prevBtn
          }
        ],
        center: [...this.slicedNBtns[this.showingSlice]],
        right: [
          {
            value: 'Next',
            action: this.nextBtn
          },
          {
            value: 'Last',
            action: this.lastBtn
          }
        ]
      }
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
        if (slice.find(btn => btn.value === page)) {
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
      this.setActivePage(this.slicedNBtns[this.showingSlice][0].value)
    },

    async nextBtn () {
      if (!this.slicedNBtns[this.showingSlice + 1]) return
      this.showingSlice += 1
      this.setActivePage(this.slicedNBtns[this.showingSlice][0].value)
    },

    async lastBtn () {
      const lastSliceIndex = this.slicedNBtns.length - 1
      const lastSlice = this.slicedNBtns[lastSliceIndex]
      const lastButton = lastSlice[this.slicedNBtns[lastSliceIndex].length - 1]
      this.showingSlice = lastSliceIndex
      await this.setActivePage(lastButton.value)
    }
  }
}
</script>

<style lang="scss" scoped>
.footer {
  display: flex;
  align-items: center;
  margin-top: 2.4rem;

  @media screen and (max-width: 1670px){
    flex-wrap: wrap;
  }
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

  @media screen and (max-width: 1670px){
    margin-right: unset;
  }
}

.footer__navigation {
  display: flex;

  @media screen and (max-width: 1670px){
    width: 100%;
    justify-content: center;
    margin-top: 1rem;
  }
}
</style>
