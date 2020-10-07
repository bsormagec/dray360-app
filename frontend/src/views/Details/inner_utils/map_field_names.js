import { uuid } from '@/utils/uuid_valid_id'

const mapFieldNamesCreator = () => {
  const mappings = {
    actual_origin: 'actual origin',
    actual_destination: 'actual destination',
    order_type: 'shipment direction',
    master_bol_mawb: 'master BOL MAWB',
    house_bol_hawb: 'house BOL MAWB',
    reference_number: 'reference number',
    pickup_number: 'pickup number',
    pickup_by_date: 'pickup date',
    pickup_by_time: 'pickup time',
    load_number: 'load number',
    unit_number: 'unit number',
    equipment_size: 'size',
    bill_to_address: 'bill to',
    hazardous: 'hazardous',
    one_way: 'one way',
    seal_number: 'seal number',
    carrier: 'SSL',
    rate_quote_number: 'rate quote number',
    shipment_designation: 'shipment designation',
    shipment_direction: 'shipment direction',
    shipment_handling: 'shipment handling',
    vessel: 'vessel',
    voyage: 'voyage',
    yard_pre_pull: 'yard pre-pull',
    purchase_order_number: 'purchase order number',
    release_number: 'release number',
    ship_comment: 'shipment notes',
    bill_comment: 'billing comments',
    line_haul: 'line haul',
    fuel_surcharge: 'fsc',
    expedite: 'expedite shipment',
    has_chassis: 'has chassis'
  }

  const mappingsBackwards = {}

  Object.keys(mappings).forEach(key => {
    mappingsBackwards[mappings[key]] = key
  })

  function getName ({ abbyName, autoIdForAbby = true, formFieldName }) {
    if (abbyName) {
      return mappings[abbyName] ? mappings[abbyName] : (autoIdForAbby ? uuid() : undefined)
    }

    if (formFieldName) {
      return mappingsBackwards[formFieldName]
    }
  }

  return {
    getName
  }
}

const mapFieldNames = mapFieldNamesCreator()
export default mapFieldNames
