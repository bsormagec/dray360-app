import { objValFromLocation } from '@/utils/obj_val_from_loc'
import { defaultsTo } from '@/utils/defaults_to'

import { pools } from '@/views/Details/inner_types'
import { shouldParseKey } from '@/views/Details/inner_utils/parse_document'
import { getFieldLocation } from '@/views/Details/inner_utils/get_field_location'

import { formModule } from '@/views/Details/inner_store/index'
import mapFieldNames from '@/views/Details/inner_utils/map_field_names'

export const parseFormValues = ({ currentOrder }) => {
  const values = {}

  /* Common fields */
  Object.keys(currentOrder).forEach(orderKey => {
    if (shouldParseKey(orderKey)) {
      const formFieldName = mapFieldNames.getName({ abbyName: orderKey, autoIdForAbby: false })
      const formFieldLocation = getFieldLocation({
        pool: formModule.state.form,
        poolType: pools.form,
        fieldName: formFieldName
      })

      if (formFieldLocation) {
        const formField = objValFromLocation({
          obj: formModule.state.form.sections,
          location: formFieldLocation,
          separator: '/'
        })

        if (formField.value !== '--') {
          if (orderKey === 'bill_to_address') {
            values[`${orderKey}_raw_text`] = formField.value
            return
          }

          if (orderKey.includes('port_ramp')) {
            values[`${orderKey}_raw_text`] = formField.value
            return
          }

          (values[orderKey] = formField.value)
        }
      }
    }
  })

  return {
    ...currentOrder,
    ...values,
    order_line_items: getLineItems(currentOrder),
    order_address_events: getAddressEvents(currentOrder)
  }
}

export function getLineItems (currentOrder) {
  const fnTypes = {
    addReq: 'requested_addition',
    deleteReq: 'requested_deletion'
  }
  const reqAction = getReqAction.call(this)
  const lineItems = []

  if (reqAction === fnTypes.addReq || !reqAction) {
    Object.entries(formModule.state.form.sections.inventory.subSections).forEach((entry, index) => {
      const orderLineItem = defaultsTo(() => currentOrder.order_line_items[index], {})
      const itemFormDescription = Object.values(entry[1].fields)[0].value

      lineItems[index] = {
        ...orderLineItem,
        id: orderLineItem.id || null, // Sending id null will create a new line item (backend related)
        t_order_id: currentOrder.id,
        contents: itemFormDescription
      }
    })
  } else if (reqAction === fnTypes.deleteReq) {
    Object.entries(currentOrder.order_line_items).forEach((entry, index) => {
      const orderLineItem = entry[1]

      const found = Object.values(formModule.state.form.sections.inventory.subSections).find(({ fields }) => {
        const item = Object.values(fields)[0]
        return orderLineItem.id === item.id
      })
      const foundItem = defaultsTo(() => Object.values(Object.values(found)[0])[0], {})

      lineItems[index] = {
        ...orderLineItem,
        id: orderLineItem.id,
        t_order_id: currentOrder.id,
        contents: foundItem.value,
        deleted_at: Object.keys(foundItem).length ? null : `${new Date(Date.now()).toISOString()}`
      }
    })
  }

  return lineItems

  function getReqAction () {
    if (Object.keys(currentOrder.order_line_items).length >
      Object.keys(formModule.state.form.sections.inventory.subSections).length) {
      return fnTypes.deleteReq
    } else if (Object.keys(currentOrder.order_line_items).length <
      Object.keys(formModule.state.form.sections.inventory.subSections).length) {
      return fnTypes.addReq
    }
  }
}

export function getAddressEvents (currentOrder) {
  const addressEvents = []

  Object.entries(formModule.state.form.sections.itinerary.rootFields).forEach((entry, index) => {
    const addrEvent = defaultsTo(() => currentOrder.order_address_events[index], {})
    const formAddrEventText = entry[1].value

    addressEvents[index] = {
      ...addrEvent,
      id: entry[1].id || null,
      t_order_id: currentOrder.id,
      t_address_raw_text: formAddrEventText
    }
  })

  return addressEvents
}
