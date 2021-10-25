export const fieldType = {
  input: 'input',
  select: 'select',
  inputSelect: 'input-select',
  switch: 'switch',
  date: 'date',
  time: 'time',
  dateTime: 'date-time',
  textArea: 'text-area',
  radio: 'radio',
  modalSelect: 'modal-select',
  modalAddress: 'modal-address',
  label: 'label',
  infoTitle: 'info-title',
  addInventoryItem: 'add-inventory-item'
}

export const eventTypes = [
  { name: 'Hook', value: 'hook' },
  { name: 'Mount', value: 'mount' },
  { name: 'Deliver', value: 'deliver' },
  { name: 'Dismount', value: 'dismount' },
  { name: 'Drop', value: 'drop' },
  { name: 'Pickup', value: 'pickup' },
]

export const shipmentDirection = [
  { name: 'Import (import)', value: 'import' },
  { name: 'Inbound (import)', value: 'import' },
  { name: 'Export (export)', value: 'export' },
  { name: 'Outbound (export)', value: 'export' },
  { name: 'Oneway (oneway)', value: 'oneway' },
  { name: 'Crosstown (oneway)', value: 'oneway' },
  { name: 'Landbridge (oneway)', value: 'oneway' },
  { name: 'Truckload (truckload)', value: 'truckload' },
]

export const booleanFields = [
  { name: 'True', value: true },
  { name: 'False', value: false },
]

export const PTFields = [
  { name: '[NONE]', value: 0 },
  { name: 'AIRBILL #', value: 5 },
  { name: 'AUTH. #', value: 16 },
  { name: 'BOOKING #', value: 18 },
  { name: 'CARRIER #', value: 13 },
  { name: 'CHASSIS #', value: 28 },
  { name: 'CLAIM #', value: 33 },
  { name: 'CONTAINER #', value: 20 },
  { name: 'CONTROL #', value: 10 },
  { name: 'CREDIT', value: 35 },
  { name: 'CUSTOMER #', value: 27 },
  { name: 'CUSTOMS #', value: 31 },
  { name: 'DEBIT', value: 36 },
  { name: 'ENTRY #', value: 17 },
  { name: 'FLATBED #', value: 1001 },
  { name: 'FWDR REF #', value: 22 },
  { name: 'I.T. #', value: 32 },
  { name: 'LOAD #', value: 29 },
  { name: 'MANIFEST #', value: 25 },
  { name: 'MASTER BL #', value: 15 },
  { name: 'ORDER #', value: 6 },
  { name: 'PO #', value: 3 },
  { name: 'PERMIT #', value: 34 },
  { name: 'PICKUP #', value: 24 },
  { name: 'PRENOTE #', value: 30 },
  { name: 'PRO #', value: 4 },
  { name: 'RAILBOX #', value: 26 },
  { name: 'RECEIPT #', value: 19 },
  { name: 'REEFER #', value: 1002 },
  { name: 'REF #', value: 2 },
  { name: 'RELEASE #', value: 9 },
  { name: 'S.O. #', value: 7 },
  { name: 'SEAL #', value: 21 },
  { name: "SHIPPER'S #", value: 1 },
  { name: 'SLIP #', value: 11 },
  { name: 'TANKER #', value: 1003 },
  { name: 'TICKET #', value: 12 },
  { name: 'TRAILER #', value: 23 },
  { name: 'TRIP #', value: 14 },
  { name: 'WORK ORDER #', value: 8 }
]
