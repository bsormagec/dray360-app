/* eslint-disable quote-props */
// import { uuid } from '@/utils/uuid_valid_id'

export function buildField ({
  name = '',
  presentationName,
  type,
  placeholder,
  options,
  children,
  width,
  value,
  isEditing,
  readonly,
  id,
  matchedAddress,
  verified,
  addressId
}) {
  const field = {
    id,
    name,
    presentationName,
    isEditing,
    readonly,
    value,
    matchedAddress,
    verified,
    addressId,
    el: {
      type,
      placeholder,
      options,
      children,
      width
    }
  }

  cleanUnusedProps()
  return field

  function cleanUnusedProps () {
    if (!name) delete field.name

    Object.keys(field.el).forEach(key => {
      if (field.el[key] === undefined) {
        delete field.el[key]
      }
    })

    if (Object.keys(field.el).length <= 0) {
      delete field.el
    }
  }
}

export function inventoryItemFields () {
  return {
    description: buildField({
      type: 'text-area',
      placeholder: 'description'
    })
  }
}
