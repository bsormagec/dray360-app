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
        :key="btn.text"
        :class="`navigation__btn ${btn.type}`"
      >
        <v-btn
          class="btn__single"
          color="primary"
          :disabled="isDots(btn.type)"
          :outlined="isOutlined(btn)"
          :text="isDots(btn.type)"
          @click="btn.action ? btn.action(btn.text) : () => {}"
        >
          {{ btn.text || '...' }}
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
      cut: 3,
      navStart: 1,
      navEnd: 3
    }
  },

  computed: {
    pagesArray () {
      const { last_page: lastPage } = this.meta()
      return arrayFromNumber({ length: mockLastPage || lastPage, from: 1 })
    },

    indicatorText () {
      const { from, to, total } = this.meta()
      const notLoaded = !from || !to || !total

      if (notLoaded) return '--'
      return `${from} - ${to} of ${total}`
    },

    allNBtns () {
      return this.pagesArray.map((n, i) => ({
        type: 'numberedButton',
        text: n,
        action: this.handleBtnNavigation
      }))
    },

    slicedNBtns () {
      return this.allNBtns.filter(({ text }) => Number(text) >= this.navStart && Number(text) <= this.navEnd)
    },

    navigationButtons () {
      const lastPage = mockLastPage || this.meta().last_page

      return [
        {
          type: 'first',
          text: 'First',
          action: async () => this.setActivePage(1)
        },
        {
          type: 'dots-left'
        },
        ...this.slicedNBtns,
        {
          type: 'dots-right'
        },
        {
          type: 'last',
          text: 'Last',
          action: async () => this.setActivePage(lastPage)
        }
      ]
    }
  },

  methods: {
    isDots (type) {
      return String(type).includes('dots')
    },

    isOutlined ({ type, text }) {
      return !this.isDots(type) && text !== this.activePage
    },

    async jumpToPage (n) {
      const page = Number(n)

      if (isNaN(page) || page <= 0) return
      await this.setActivePage(page)
    },

    handleBtnNavigation (n) {
      /*
        TODO: - Refactor this function*
              - Add and write logic for prev/next btns
              - write logic for first/last btns
      */
      const isLastItem = n === this.navEnd
      const itemNextToEnd = this.allNBtns[this.navEnd]
      const shouldMove = isLastItem && itemNextToEnd
      const cutFromStart = () => this.navStart + (this.cut - 1)
      const ableToCutFromStart = () => this.allNBtns[cutFromStart()]

      if (shouldMove) {
        this.navStart = this.navEnd
        if (ableToCutFromStart()) {
          this.navEnd = cutFromStart()
        } else {
          this.navEnd = this.allNBtns.length
        }
      }

      this.setActivePage(n)
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

  .navigation__btn {
    &.numberedButton:not(:first-child) .btn__single,
    &.numberedButton:not(:last-child) .btn__single {
      border-radius: unset;
    }

    &.numberedButton-last .btn__single {
      border-top-right-radius: 0.4rem !important;
      border-bottom-right-radius: 0.4rem !important;
    }

    &.numberedButton-first .btn__single {
      border-top-left-radius: 0.4rem !important;
      border-bottom-left-radius: 0.4rem !important;
    }
  }
}
</style>
