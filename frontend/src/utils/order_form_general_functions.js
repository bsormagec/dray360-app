import { defaultsTo } from '@/utils/defaults_to'

export function getHighlights (order) {
  const highlights = {}

  for (const key in order) {
    if (!keyShouldBeParsed(key)) {
      continue
    }

    let highlightKey = key
    let getOcrKey = key

    if (key === 'order_address_events') {
      order[key].forEach((orderAddressEvent, i) => {
        highlightKey = `order_address_events.${i}`
        getOcrKey = highlightKey
      })
    } else if (key === 'order_line_items') {
      order[key].forEach((orderLineItem, i) => {
        highlightKey = `order_line_items.${i}`
        getOcrKey = 'order_line_items'
      })
    }

    const ocrData = getOcrData(getOcrKey, order.ocr_data)

    if (ocrData === undefined) {
      continue
    }

    highlights[highlightKey] = {
      ...ocrData,
      hover: false,
      edit: false,
      hoverTimeout: null
    }
  }

  return highlights
}

export function keyShouldBeParsed (key) {
  const invalidEndings = [
    'id',
    '_id',
    '_raw_text',
    '_verified',
    'ocr_data',
    'ocr_request',
    '_at'
  ]

  return invalidEndings.reduce((acc, crr) => acc && !key.includes(crr), true)
}

export function getOcrData (key, ocrData) {
  if (key.includes('order_address_events')) {
    const found = Object.values(
      defaultsTo(() => ocrData.fields, {})
    ).find(field => {
      return defaultsTo(() => field.d360_name, '').includes(`${key.split('.')[1]}`) && !field.name.includes('_type')
    })

    return defaultsTo(() => found.ocr_region, {})
  } else if (key.includes('order_line_items')) {
    return defaultsTo(() => ocrData.fields.contents.ocr_region, {})
  } else if (key.includes('bill_to_address')) {
    return defaultsTo(() => ocrData.fields.bill_to_address.ocr_region, {})
  }

  return defaultsTo(() => ocrData.fields[key].ocr_region, {})
}

export function formatAddress (address) {
  if (address === null) return '--'

  const strSpacer = (str, spacer) => {
    return str ? str + spacer : ''
  }

  // eslint-disable-next-line camelcase
  const { location_name, address_line_1, address_line_2, city, state, postal_code } = address

  return `
    ${strSpacer(location_name, ' <br>')}
    ${strSpacer(address_line_1, ' <br>')}
    ${strSpacer(address_line_2, ' <br>')}
    ${strSpacer(city, ', ')}${strSpacer(state, ' ')}${strSpacer(postal_code, '')}
  `
}

export function parseChanges ({ path, value, originalOrder }) {
  let changes = {}
  if (path === 'bill_to_address') {
    changes = {
      bill_to_address_id: value.id,
      bill_to_address_verified: true
    }
  } else if (path.includes('order_address_events.')) {
    const index = path.split('.')[1]
    // eslint-disable-next-line camelcase
    const { order_address_events } = originalOrder

    order_address_events[index] = {
      ...order_address_events[index],
      t_address_id: value.t_address_id,
      t_address_verified: true
    }

    changes = {
      order_address_events: order_address_events
    }
  } else if (path.includes('order_line_items.')) {
    const index = path.split('.')[1]
    // eslint-disable-next-line camelcase
    const { order_line_items } = originalOrder

    order_line_items[index].contents = value

    changes = { order_line_items }
  } else {
    changes = { [path]: value }
  }

  return changes
}
