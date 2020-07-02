import { uuid } from '@/utils/uuid_valid_id'

const mapFieldNamesCreator = () => {
  const mappings = {
    port_ramp_of_origin_address: 'port ramp of origin',
    port_ramp_of_destination_address: 'port ramp of destination',
    order_type: 'shipment direction',
    master_bol_mawb: 'master BOL MAWB',
    house_bol_hawb: 'house BOL MAWB',
    reference_number: 'reference number',
    unit_number: 'unit number',
    equipment_size: 'size',
    bill_to_address: 'bill to',
    equipment: 'type',
    equipment_type: 'equipment',
    hazardous: 'hazardous',
    one_way: 'one way',
    owner_or_ss_company: 'owner or SS company',
    rate_quote_number: 'rate quote number',
    shipment_designation: 'shipment designation',
    shipment_direction: 'shipment direction',
    vessel: 'vessel',
    voyage: 'voyage',
    yard_pre_pull: 'yard pre-pull'
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
