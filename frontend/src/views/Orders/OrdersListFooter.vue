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
import arrayFromNumber from '@/utils/array_from_number'
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
    requestPage: {
      type: Function,
      required: true
    }
  },

  data () {
    const { meta } = this[providerStateName]

    return {
      meta,
      cut: 3,
      activeButton: 1
    }
  },

  computed: {
    pagesArray () {
      return arrayFromNumber({ length: this.meta().last_page, from: 1 })
    },

    allNBtns () {
      const action = (n) => {
        this.activeButton = n
        this.requestPage(n)
      }

      return this.pagesArray.map((n) => ({
        type: 'numberedButton',
        value: n,
        action
      }))
    },

    slicedNBtns () {
      const sliced = []

      this.allNBtns.forEach((btn) => {
        if (this.activeButton == 1) { // eslint-disable-line
          return btn.value >= this.activeButton &&
          btn.value <= this.cut &&
          sliced.push(btn)
        }

        if (this.activeButton == this.allNBtns.length) { // eslint-disable-line
          return btn.value > (this.allNBtns.length - this.cut) &&
          sliced.push(btn)
        }

        return (btn.value === this.activeButton - 1 ||
          btn.value === this.activeButton + 1 ||
          btn.value === this.activeButton) &&
          sliced.push(btn)
      })

      return sliced
    },

    navigationButtons () {
      return {
        left: [
          {
            value: 'First',
            action: this.firstBtn,
            disabled: this.activeButton == 1 // eslint-disable-line
          }
        ],
        center: this.slicedNBtns,
        right: [
          {
            value: 'Last',
            action: this.lastBtn,
            disabled: this.activeButton == this.allNBtns.length // eslint-disable-line
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

  beforeMount () {
    const urlPage = this.$route.query.page
    if (urlPage) this.jumpToPage(urlPage)
  },

  methods: {
    async jumpToPage (n) {
      const page = Number(n)
      if (isNaN(page) || page <= 0 || page === this.activeButton || page > this.pagesArray.length) return

      this.activeButton = parseInt(n)
      await this.requestPage(page)
    },

    async firstBtn () {
      this.activeButton = 1
      await this.requestPage(1)
    },

    async lastBtn () {
      this.activeButton = this.allNBtns.length
      await this.requestPage(this.activeButton)
    }
  }
}
</script>

<style lang="scss" scoped>
.footer {
  display: flex;
  align-items: center;
  margin-top: 2.4rem;

  @media screen and (min-width: map-get($breakpoints , lg)) {
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
  }
}

.footer__indicator {
  margin-right: auto;
  font-size: 1.44rem !important;
  font-weight: bold;
  display: none;

  @media screen and (min-width: map-get($breakpoints , lg)) {
    display: flex;
  }
}

.footer__jump {
  display: none;
  align-items: center;
  width: 14rem;
  margin-right: 2.1rem;

  @media screen and (min-width: map-get($breakpoints , lg)) {
    display: flex;
  }

  span {
    min-width: 8rem;
    font-size: 1.3rem !important;
    font-weight: bold;
    margin-right: 0.8rem;
  }
}

.footer__navigation {
  display: flex;
  margin: 0 auto;

  @media screen and (min-width: map-get($breakpoints , lg)) {
    margin: unset;
  }
}
</style>
