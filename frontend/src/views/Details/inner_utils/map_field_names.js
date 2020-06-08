import { uuid } from '@/utils/uuid_valid_id'

export default (abbyName) => {
  let formFieldName

  switch (abbyName) {
    case 'port_ramp_of_origin_address':
      formFieldName = 'port ramp of origin'
      break
    case 'port_ramp_of_destination_address':
      formFieldName = 'port ramp of destination'
      break
    case 'order_type':
      formFieldName = 'shipment direction'
      break
    case 'master_bol_mawb':
      formFieldName = 'master BOL MAWB'
      break
    case 'house_bol_hawb':
      formFieldName = 'house BOL MAWB'
      break
    case 'reference_number':
      formFieldName = 'reference number'
      break
    case 'unit_number':
      formFieldName = 'unit number'
      break
    case 'equipment_size':
      formFieldName = 'size'
      break
    case 'bill_to_address':
      formFieldName = 'bill to'
      break
    case 'equipment':
      formFieldName = 'type'
      break
    case 'equipment_type':
      formFieldName = 'equipment'
      break
    case 'hazardous':
      formFieldName = 'hazardous'
      break
    case 'one_way':
      formFieldName = 'one way'
      break
    case 'owner_or_ss_company':
      formFieldName = 'owner or SS company'
      break
    case 'rate_quote_number':
      formFieldName = 'rate quote number'
      break
    case 'shipment_designation':
      formFieldName = 'shipment designation'
      break
    case 'shipment_direction':
      formFieldName = 'shipment direction'
      break
    case 'vessel':
      formFieldName = 'vessel'
      break
    case 'voyage':
      formFieldName = 'voyage'
      break
    case 'yard_pre_pull':
      formFieldName = 'yard pre-pull'
      break
    default:
      formFieldName = uuid()
      break
  }

  return formFieldName
}
