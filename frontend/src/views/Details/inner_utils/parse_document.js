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
// import { uuid } from '@/utils/uuid_valid_id'
import { buildField } from '@/views/Details/inner_utils/example_form'

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
          // const evtName = defaultsTo(() => (evt.event_number + ':' + evt.unparsed_event_type).toLowerCase(), 'EventType Unknown:' + evt.event_number)
          const evtValue = getMatchedAddress(evt)

          const addrEvents = formModule.state.form.sections.itinerary.rootFields
          Vue.set(
            addrEvents,
            evtName,
            buildField({
              type: 'modal-address',
              isEditing: true,
              readonly: false,
              id: evt.id,
              matchedAddress: evtValue
            })
          )

          highlights[evtName] = {
            ...getOcrData(`order_address_events.${i}`, data),
            name: evtName,
            value: evt.t_address_raw_text
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
        highlights[key] = {
          ...getOcrData(key, data),
          name: mapFieldNames.getName({ abbyName: key }),
          value: data.bill_to_address_raw_text,
          matchedAddress: defaultsTo(() => `${data.bill_to_address?.location_name} \n ${data.bill_to_address?.address_line_1} \n ${data.bill_to_address?.address_line_2} \n ${data.bill_to_address?.city}, ${data.bill_to_address?.state} ${data.bill_to_address?.postal_code} `, '--')
        }
      } else if (key.includes('port_ramp')) {
        /* eslint camelcase: 0 */
        const valueForMatched = defaultsTo(() => data[key], {})
        const { location_name, address_line_1, city, state, postal_code } = valueForMatched // matched text
        const matchedText = `${strSpacer(location_name, ' ')}${strSpacer(address_line_1, ' ')}${strSpacer(city, ', ')}${strSpacer(state, ' ')}${strSpacer(postal_code, ' ')}`

        /* Matched address */
        const origin = formModule.state.form.sections.shipment.subSections.origin.fields
        Vue.set(
          origin,
          `${portRampKeyParser(key)}`,
          buildField({
            type: 'modal-address',
            isEditing: true,
            readonly: false,
            matchedAddress: matchedText,
            value: defaultsTo(() => data[`${key}_raw_text`], '--')
          })
        )
        /* -- */

        highlights[key] = {
          ...getOcrData(key, data),
          name: mapFieldNames.getName({ abbyName: key })
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

function portRampKeyParser (key) {
  return key.includes('destination') ? 'Port Ramp of Destination' : 'Port Ramp of Origin'
}

function getMatchedAddress (evt) {
  return defaultsTo(() => `${evt.address?.location_name} \n ${evt.address?.address_line_1} \n ${evt.address?.address_line_2} \n ${evt.address?.city}, ${evt.address?.state} ${evt.address?.postal_code}`, '--')
}
