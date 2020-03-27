import Vue from 'vue'
import Vuex from 'vuex'
import { render } from '@testing-library/vue'
import Orders from '@/views/Orders/Orders'
import storeModule from '@/store/modules/orders'

Vue.use(Vuex)

describe('Orders', () => {
  const store = {
    modules: {
      orders: storeModule
    }
  }

  it('renders', () => {
    const { getByTestId } = render(Orders, { store })
    expect(getByTestId('Orders')).toBeTruthy()
  })
})
