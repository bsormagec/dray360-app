import { render } from '@testing-library/vue'
import Orders from '@/views/Orders/Orders'
import store from '@/store/modules/orders'

describe('Orders', () => {
  it('renders', () => {
    const { getByTestId } = render(Orders, { store })
    expect(getByTestId('test-orders')).toBeTruthy()
  })
})
