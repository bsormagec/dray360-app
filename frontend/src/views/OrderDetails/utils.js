/* eslint-disable camelcase */

const countFieldmapFilters = fieldMap => {
  return 0 + (!!fieldMap.shipment_direction_filter) + (!!fieldMap.bill_to_address_filter)
}

export const getMapForFilters = ({ fieldMaps, d3CanonName, shipmentDirection, billToAddress }) => {
  shipmentDirection = (shipmentDirection || '').trim()
  billToAddress = (billToAddress || '').trim()
  const canonFieldmaps = Object.keys(fieldMaps)
    .map(key => fieldMaps[key])
    .filter(fieldMap => fieldMap.d3canon_name === d3CanonName)
    .sort((a, b) => (countFieldmapFilters(a) - countFieldmapFilters(b)))

  for (const i in canonFieldmaps) {
    const {
      shipment_direction_filter = null,
      bill_to_address_filter = null
    } = canonFieldmaps[i]

    if (!!shipment_direction_filter && !shipment_direction_filter.includes(shipmentDirection)) {
      continue
    }

    if (!!bill_to_address_filter && !bill_to_address_filter.split('|').some(filter => billToAddress.includes(filter))) {
      continue
    }
    console.log(canonFieldmaps[i])
    return canonFieldmaps[i]
  }
}
