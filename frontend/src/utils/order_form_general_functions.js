import { defaultsTo } from '@/utils/defaults_to'
import get from 'lodash/get'
import includes from 'lodash/includes'

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

        highlights[highlightKey] = baseHighlight(getOcrData(getOcrKey, order.ocr_data))
      })
      continue
    } else if (key === 'order_line_items') {
      order[key].forEach((orderLineItem, i) => {
        highlightKey = `order_line_items.${i}`
        getOcrKey = 'order_line_items'

        if (i === 0) {
          highlights[`${highlightKey}.contents`] = baseHighlight(getOcrData(getOcrKey, order.ocr_data))
        } else {
          highlights[`${highlightKey}.contents`] = baseHighlight({ recognizedText: '' })
        }
        highlights[`${highlightKey}.weight`] = baseHighlight({ recognizedText: '' })
        highlights[`${highlightKey}.quantity`] = baseHighlight({ recognizedText: '' })
      })
      continue
    }

    highlights[highlightKey] = baseHighlight(getOcrData(getOcrKey, order.ocr_data))
  }

  return highlights
}

export function baseHighlight (ocrData) {
  return {
    ...ocrData,
    hover: false,
    edit: false,
    hoverTimeout: null,
    loading: false
  }
}

export function keyShouldBeParsed (key) {
  const shouldNotBeIgnored = [
    't_equipment_type_id',
    'chassis_equipment_type_id',
    'tms_template_dictid',
    'itgcontainer_dictid',
    'carrier_dictid',
    'vessel_dictid',
    'cc_loadtype_dictid',
    'cc_orderstatus_dictid',
    'cc_haulclass_dictid',
    'cc_orderclass_dictid',
    'cc_loadedempty_dictid',
    'termdiv_dictid',
    'cc_containersize_dictid',
    'cc_containertype_dictid',
  ]

  if (shouldNotBeIgnored.includes(key)) {
    return true
  }

  const invalidEndings = [
    'id',
    '_id',
    '_raw_text',
    '_verified',
    'ocr_data',
    'ocr_request',
    '_at'
  ]

  return invalidEndings.reduce((acc, crr) => acc && !key.endsWith(crr), true)
  // return invalidEndings.reduce((acc, crr) => acc && !key.includes(crr), true)
}

export function getOcrData (key, ocrData) {
  if (key.includes('order_address_events')) {
    const found = Object.values(
      get(ocrData, 'fields', {})
    ).find(field => {
      return get(field, 'd360_name', '').includes(`${key.split('.')[1]}`) && !get(field, 'name', '').includes('_type')
    })

    return defaultsTo(() => found.ocr_region, {})
  } else if (key.includes('order_line_items')) {
    return defaultsTo(() => ocrData.fields.contents.ocr_region, {})
  } else if (key.includes('bill_to_address')) {
    return defaultsTo(() => ocrData.fields.bill_to_address.ocr_region, {})
  }

  return defaultsTo(() => ocrData.fields[key].ocr_region, {})
}

export function getNonPDFHighlightsParsedFieldName (originalFieldKey) {
  const eventRgx = /event([0-9]).*/
  if (originalFieldKey.match(eventRgx)) {
    const matchIndex = originalFieldKey.match(eventRgx)[1]
    return `order_address_events.${Number(matchIndex) - 1}`
  }
  return originalFieldKey
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
  const simpleVerifiableFields = [
    {
      field: 't_equipment_type_id',
      boolean_field: 'equipment_type_verified'
    },
    {
      field: 'chassis_equipment_type_id',
      boolean_field: 'chassis_equipment_type_verified'
    },
    {
      field: 'tms_template_dictid',
      boolean_field: 'tms_template_dictid_verified'
    },
    {
      field: 'carrier_dictid',
      boolean_field: 'carrier_dictid_verified'
    },
    {
      field: 'vessel_dictid',
      boolean_field: 'vessel_dictid_verified'
    },
    {
      field: 'cc_loadtype_dictid',
      boolean_field: 'cc_loadtype_dictid_verified'
    },
    {
      field: 'cc_orderstatus_dictid',
      boolean_field: 'cc_orderstatus_dictid_verified'
    },
    {
      field: 'cc_haulclass_dictid',
      boolean_field: 'cc_haulclass_dictid_verified'
    },
    {
      field: 'cc_orderclass_dictid',
      boolean_field: 'cc_orderclass_dictid_verified'
    },
    {
      field: 'cc_loadedempty_dictid',
      boolean_field: 'cc_loadedempty_dictid_verified'
    },
    {
      field: 'termdiv_dictid',
      boolean_field: 'termdiv_dictid_verified'
    },
    {
      field: 'cc_containersize_dictid',
      boolean_field: 'cc_containersize_dictid_verified'
    },
    {
      field: 'cc_containertype_dictid',
      boolean_field: 'cc_containertype_dictid_verified'
    },
  ]

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
      ...value
    }

    changes = {
      order_address_events: order_address_events
    }
  } else if (path.includes('order_line_items.')) {
    const [key, index, field] = path.split('.')
    if (field === undefined || field === null) {
      throw new Error('For order line items the field definition is required')
    }
    // eslint-disable-next-line camelcase
    const { order_line_items } = originalOrder

    order_line_items[index][field] = value

    changes = { order_line_items }
  } else if (simpleVerifiableFields.findIndex(item => item.field === path) !== -1) {
    const index = simpleVerifiableFields.findIndex(item => item.field === path)
    const item = simpleVerifiableFields[index]

    changes = {
      [item.field]: value,
      [item.boolean_field]: true
    }
  } else {
    changes = { [path]: value }
  }

  return changes
}
