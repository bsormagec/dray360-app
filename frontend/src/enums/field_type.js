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
