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
      />
    </div>

    <div class="footer__navigation">
      <div
        v-for="btn in navigationButtons"
        :key="btn.text"
        class="navigation__btn"
      >
        <v-btn
          :class="`btn__single ${btn.id}`"
          color="primary"
          :disabled="isDots(btn.id)"
          :outlined="!isDots(btn.id)"
          :text="isDots(btn.id)"
        >
          {{ btn.text || '...' }}
        </v-btn>
      </div>
    </div>
  </div>
</template>

<script>
import arrayFromNumber from '@/utils/arrayFromNumber'
import { providerStateName, providerMethodsName } from '@/views/Orders/inner_types'

export default {
  name: 'OrdersListFooter',

  inject: [providerStateName, providerMethodsName],

  data () {
    const { meta } = this[providerStateName]
    const { fetchOrdersList } = this[providerMethodsName]

    return {
      meta,
      fetchOrdersList
    }
  },

  computed: {
    indicatorText () {
      const { from, to, total } = this.meta()
      const notLoaded = !from || !to || !total

      if (notLoaded) return '--'
      return `${from} - ${to} of ${total}`
    },

    pagesArray () {
      const { last_page: lastPage } = this.meta()
      return arrayFromNumber({ length: lastPage, from: 1 })
    },

    navigationButtons () {
      return [
        {
          id: 'first',
          text: 'First',
          action: () => { }
        },
        {
          id: 'dots-left'
        },
        ...this.pagesArray.map((n, i) => ({
          id: this.navBtnId({ index: i, length: this.pagesArray.length }),
          text: n,
          action: () => { }
        })),
        {
          id: 'dots-right'
        },
        {
          id: 'last',
          text: 'Last',
          action: () => { }
        }
      ]
    }
  },

  methods: {
    isDots (id) {
      return String(id).includes('dots')
    },

    navBtnId ({ index, length }) {
      const isLeft = index === 0
      const isRight = index === length - 1

      if (isLeft) return 'navbtn-left'
      if (isRight) return 'navbtn-right'
      return 'navbtn'
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
  width: 12rem;
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

  .btn__single {
    min-width: 3rem !important;

    &.navbtn {
      border-radius: unset;
    }

    &.navbtn-left {
      border-top-right-radius: unset;
      border-bottom-right-radius: unset;
    }

    &.navbtn-right {
      border-top-left-radius: unset;
      border-bottom-left-radius: unset;
    }
  }
}
</style>
