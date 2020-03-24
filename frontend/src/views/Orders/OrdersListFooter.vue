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
      <v-btn
        v-for="btn in navigationButtons"
        :key="btn.id"
        class="navigation__btn"
        color="primary"
        outlined
      >
        {{ btn.text }}
      </v-btn>
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

    navigationButtons () {
      const { last_page: lastPage } = this.meta()

      return [
        {
          id: 'first',
          text: 'First',
          action: () => { }
        },
        {
          id: 'prev',
          text: 'Prev',
          action: () => { }
        },
        ...arrayFromNumber({ length: lastPage, from: 1 }).map(n => ({
          id: n,
          text: n,
          action: () => { }
        })),
        {
          id: 'next',
          text: 'Next',
          action: () => { }
        },
        {
          id: 'last',
          text: 'Last',
          action: () => { }
        }
      ]
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
  .navigation__btn:not(:last-child) {
    margin-right: 1rem;
    min-width: 4.4rem;
  }
}
</style>
