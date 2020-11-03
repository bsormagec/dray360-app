/* eslint-disable camelcase */
/*
  [
    {
      image: "url"
      highlights: [
        {
          bottom: 0,
          left: 0,
          right: 0,
          top: 0,
          name: 'name',
          value: 'value'
        }
      ]
    }
  ]
*/

import Vue from 'vue'
import mapFieldNames from '@/views/Details/inner_utils/map_field_names'
import { formModule } from '@/views/Details/inner_store/index'
import { defaultsTo } from '@/utils/defaults_to'
import get from 'lodash/get'
import { buildField } from '@/views/Details/inner_utils/form_functions'

export const parse = ({ data }) => {
  return [
    {
      image: defaultsTo(() => data.ocr_data.page_index_filenames.value[1].presigned_download_uri, '#'),
      highlights: getHighlights(data)
    }
  ]
}

function getHighlights (data) {
  const highlights = {}

  for (const [key, value] of Object.entries(data)) {
    if (shouldParseKey(key)) {
      if (key === 'order_address_events') {
        const evts = defaultsTo(() => data.order_address_events, [])
        evts.forEach((evt, i) => {
          const evtName = `${evt.event_number} : ${evt.unparsed_event_type || 'Unknown'}`.toLowerCase()
          const addrEvents = formModule.state.form.sections.itinerary.rootFields
          Vue.set(
            addrEvents,
            evtName,
            buildField({
              type: 'modal-address',
              isEditing: true,
              readonly: false,
              id: evt.id,
              addressId: evt.t_address_id,
              matchedAddress: formatAddress(evt.address),
              verified: evt.t_address_verified
            })
          )

          highlights[evtName] = {
            ...getOcrData(`order_address_events.${i}`, data),
            name: evtName,
            value: defaultsTo(() => evt.t_address_raw_text, '--')
          }
        })
      } else if (key === 'order_line_items') {
        const items = defaultsTo(() => data.order_line_items, [])
        items.forEach((item, i) => {
          const itemName = `ìtem ${i + 1}`
          const itemValue = defaultsTo(() => item.contents, '--')

          const lineItems = formModule.state.form.sections.inventory.subSections
          Vue.set(
            lineItems,
            itemName,
            {
              fields: {
                [itemName]: buildField({
                  presentationName: 'description',
                  type: 'text-area',
                  placeholder: 'description',
                  value: itemValue,
                  id: item.id
                })
              }
            }
          )

          highlights[itemName] = {
            ...getOcrData('order_line_items', data),
            name: itemName,
            value: itemValue
          }
        })
      } else if (key === 'bill_to_address') {
        const charges = formModule.state.form.sections.shipment.subSections.charges.fields
        Vue.set(
          charges,
          'bill to',
          buildField({
            type: 'modal-address',
            isEditing: true,
            readonly: false,
            addressId: get(data, 'bill_to_address.id', null),
            matchedAddress: formatAddress(data.bill_to_address),
            verified: data.bill_to_address_verified
          })
        )

        highlights[key] = {
          ...getOcrData(key, data),
          name: mapFieldNames.getName({ abbyName: key }),
          value: defaultsTo(() => data.bill_to_address_raw_text, '--')
        }
      } else {
        highlights[key] = {
          ...getOcrData(key, data),
          name: mapFieldNames.getName({ abbyName: key }),
          value: defaultsTo(() => value, '--')
        }
      }
    }
  }

  return Object.values(highlights)
}

export function formatAddress (address) {
  if (address === null) return '--'

  const { location_name, address_line_1, address_line_2, city, state, postal_code } = address
  return `
    ${strSpacer(location_name, ' <br>')}
    ${strSpacer(address_line_1, ' <br>')}
    ${strSpacer(address_line_2, ' <br>')}
    ${strSpacer(city, ', ')}${strSpacer(state, ' ')}${strSpacer(postal_code, '')}
  `
}

export function shouldParseKey (key) {
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

function getOcrData (key, data) {
  if (key.includes('order_address_events')) {
    const found = Object.values(
      defaultsTo(() => data.ocr_data.fields, {})
    ).find(field => {
      return defaultsTo(() => field.d360_name, '').includes(`${key.split('.')[1]}`) && !field.name.includes('_type')
    })

    return defaultsTo(() => found.ocr_region, {})
  } else if (key.includes('order_line_items')) {
    return defaultsTo(() => data.ocr_data.fields.contents.ocr_region, {})
  } else if (key.includes('bill_to_address')) {
    return defaultsTo(() => data.ocr_data.fields.bill_to_address.ocr_region, {})
  } else {
    return defaultsTo(() => data.ocr_data.fields[key].ocr_region, {})
  }
}

function strSpacer (str, spacer) {
  return str ? str + spacer : ''
}
