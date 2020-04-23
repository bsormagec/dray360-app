export const exampleForm = {
  sections: {
    shipment: {
      rootFields: {
        'shipment designation': buildField({
          type: 'input',
          placeholder: 'international'
        }),
        'shipment direction': buildField({
          type: 'select',
          options: ['a', 'b', 'c']
        }),
        'shipment handling': buildField({
          type: 'select',
          options: ['a', 'b', 'c']
        }),
        'one way': buildField({
          type: 'switch'
        }),
        'expedite shipment': buildField({
          type: 'switch'
        }),
        hazardous: buildField({
          type: 'switch'
        })
      },
      subSections: {
        equipment: {
          fields: {
            type: buildField({
              type: 'input',
              placeholder: 'container'
            }),
            'unit number': buildField({
              type: 'input',
              placeholder: 'AAAU656578'
            }),
            equipment: buildField({
              type: 'input',
              placeholder: 'GP-General Purpose'
            }),
            size: buildField({
              type: 'input',
              placeholder: '20 ft'
            }),
            'yard pre-pull': buildField({
              type: 'switch'
            }),
            'has chassis': buildField({
              type: 'switch'
            }),
            'owner or SS company': buildField({
              type: 'input',
              placeholder: 'ACL'
            })
          }
        },
        origin: {
          fields: {
            'reference number': buildField({
              type: 'input',
              placeholder: 'reference number'
            }),
            'rate quote number': buildField({
              type: 'input',
              placeholder: 'rate quote number'
            }),
            'port/ramp of origin': buildField({
              type: 'input',
              placeholder: 'port/ramp of origin'
            }),
            'port/ramp of destination': buildField({
              type: 'input',
              placeholder: 'port/ramp of destination'
            }),
            vessel: buildField({
              type: 'input',
              placeholder: 'vessel'
            }),
            voyage: buildField({
              type: 'input',
              placeholder: 'voyage'
            }),
            'master BOL / MAWB': buildField({
              type: 'input',
              placeholder: 'master BOL / MAWB'
            }),
            'house BOL / MAWB': buildField({
              type: 'input',
              placeholder: 'house BOL / MAWB'
            }),
            '(Est) arrival': buildField({
              type: 'date-time'
            }),
            'last free day': buildField({
              type: 'date-time'
            })
          }
        },
        billing: {
          fields: {
            'bill to': buildField({
              type: 'modal-select',
              placeholder: 'select address',
              options: addresses()
            }),
            'company name': buildField({
              type: 'input',
              placeholder: 'company name'
            }),
            address: buildField({
              type: 'text-area',
              placeholder: 'address'
            }),
            city: buildField({
              type: 'input',
              placeholder: 'city'
            }),
            state: buildField({
              type: 'input',
              placeholder: 'state'
            }),
            zip: buildField({
              type: 'input',
              placeholder: 'zip'
            }),
            'contact name': buildField({
              type: 'input',
              placeholder: 'contact name'
            }),
            phone: buildField({
              type: 'input',
              placeholder: 'phone'
            }),
            ext: buildField({
              type: 'input',
              placeholder: 'ext'
            }),
            email: buildField({
              type: 'input',
              placeholder: 'email'
            })
          }
        }
      }
    },
    itinerary: {
      subSections: {
        'hook: rail or port terminal': {
          fields: {
            hook: buildField({
              type: 'modal-select',
              placeholder: 'select address',
              options: addresses()
            }),
            'company name': buildField({
              type: 'input',
              placeholder: 'company name'
            }),
            address: buildField({
              type: 'text-area',
              placeholder: 'address'
            }),
            city: buildField({
              type: 'input',
              placeholder: 'city'
            }),
            state: buildField({
              type: 'input',
              placeholder: 'state'
            }),
            zip: buildField({
              type: 'input',
              placeholder: 'zip'
            }),
            'contact name': buildField({
              type: 'input',
              placeholder: 'contact name'
            }),
            phone: buildField({
              type: 'input',
              placeholder: 'phone'
            }),
            ext: buildField({
              type: 'input',
              placeholder: 'ext'
            }),
            email: buildField({
              type: 'input',
              placeholder: 'email'
            }),
            notes: buildField({
              type: 'text-area',
              placeholder: 'notes'
            }),
            hours: buildField({
              type: 'input',
              placeholder: 'hours'
            })
          }
        },
        'deliver: container to': {
          fields: {
            deliver: buildField({
              type: 'modal-select',
              placeholder: 'select address',
              options: addresses()
            }),
            'company name': buildField({
              type: 'input',
              placeholder: 'company name'
            }),
            address: buildField({
              type: 'text-area',
              placeholder: 'address'
            }),
            city: buildField({
              type: 'input',
              placeholder: 'city'
            }),
            state: buildField({
              type: 'input',
              placeholder: 'state'
            }),
            zip: buildField({
              type: 'input',
              placeholder: 'zip'
            }),
            'contact name': buildField({
              type: 'input',
              placeholder: 'contact name'
            }),
            phone: buildField({
              type: 'input',
              placeholder: 'phone'
            }),
            ext: buildField({
              type: 'input',
              placeholder: 'ext'
            }),
            email: buildField({
              type: 'input',
              placeholder: 'email'
            }),
            notes: buildField({
              type: 'text-area',
              placeholder: 'notes'
            }),
            hours: buildField({
              type: 'input',
              placeholder: 'hours'
            }),
            'delivery instructions': buildField({
              type: 'radio',
              options: [
                buildField({ name: 'call for appointment' }),
                buildField({
                  name: 'deliver between',
                  children: [
                    buildField({ name: 'start', type: 'time', width: '48%' }),
                    buildField({ name: 'end', type: 'time', width: '48%' }),
                    buildField({
                      name: 'instructions',
                      type: 'text-area',
                      width: '100%',
                      placeholder: 'delivery instructions'
                    })
                  ]
                })
              ]
            })
          }
        },
        'dismount: return empty to depot': {
          fields: {
            dismount: buildField({
              placeholder: 'select address',
              type: 'modal-select',
              options: addresses()
            }),
            'company name': buildField({
              type: 'input',
              placeholder: 'company name'
            }),
            address: buildField({
              type: 'text-area',
              placeholder: 'address'
            }),
            city: buildField({
              type: 'input',
              placeholder: 'city'
            }),
            state: buildField({
              type: 'input',
              placeholder: 'state'
            }),
            zip: buildField({
              type: 'input',
              placeholder: 'zip'
            }),
            'contact name': buildField({
              type: 'input',
              placeholder: 'contact name'
            }),
            phone: buildField({
              type: 'input',
              placeholder: 'phone'
            }),
            ext: buildField({
              type: 'input',
              placeholder: 'ext'
            }),
            email: buildField({
              type: 'input',
              placeholder: 'email'
            }),
            notes: buildField({
              type: 'text-area',
              placeholder: 'notes'
            }),
            hours: buildField({
              type: 'input',
              placeholder: 'hours'
            }),
            'pickup instructions': buildField({
              type: 'radio',
              options: [
                buildField({ name: 'call for appointment' }),
                buildField({
                  name: 'pickup on',
                  children: [
                    buildField({ name: 'date', type: 'date' }),
                    buildField({ name: 'between', type: 'label', width: '100%' }),
                    buildField({ name: 'start', type: 'time', width: '48%' }),
                    buildField({ name: 'end', type: 'time', width: '48%' }),
                    buildField({
                      name: 'instructions',
                      type: 'text-area',
                      width: '100%',
                      placeholder: 'delivery instructions'
                    })
                  ]
                })
              ]
            })
          }
        }
      }
    },
    inventory: {
      rootFields: {
        quantity: buildField({
          type: 'input',
          placeholder: 'quantity'
        }),
        'unit of measure': buildField({
          type: 'select',
          options: ['a', 'b', 'c']
        }),
        description: buildField({
          type: 'input',
          placeholder: 'description'
        }),
        'weight/unit': buildField({
          type: 'input-select',
          options: ['a', 'b', 'c']
        }),
        'total weight': buildField({
          type: 'input',
          placeholder: 'total weight'
        }),
        hazardous: buildField({
          type: 'switch',
          children: [
            buildField({
              name: 'hazardous item information',
              type: 'info-title'
            }),
            buildField({
              name: 'contact name',
              type: 'input',
              placeholder: 'contact name'
            }),
            buildField({
              name: 'phone',
              type: 'input',
              placeholder: 'phone'
            }),
            buildField({
              name: 'UN code',
              type: 'input',
              placeholder: 'UN code'
            }),
            buildField({
              name: 'qualifier',
              type: 'input',
              placeholder: 'qualifier'
            }),
            buildField({
              name: 'flashpoint temp',
              type: 'input',
              placeholder: 'flashpoint temp'
            }),
            buildField({
              name: 'UN name',
              type: 'input',
              placeholder: 'UN name'
            }),
            buildField({
              name: 'HAZ class',
              type: 'input',
              placeholder: 'HAZ class'
            }),
            buildField({
              name: 'IMDG page no',
              type: 'input',
              placeholder: 'IMDG page no'
            }),
            buildField({
              name: 'packaging group',
              type: 'input',
              placeholder: 'packaging group'
            }),
            buildField({
              name: 'description',
              type: 'text-area',
              placeholder: 'description'
            })
          ]
        })
      }
    },
    notes: {
      rootFields: {
        notes: buildField({
          type: 'text-area',
          placeholder: 'notes'
        })
      }
    }
  }
}

function buildField ({
  name,
  type,
  placeholder,
  options,
  children,
  width
}) {
  const field = {
    name,
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

function addresses () {
  return [
    {
      companyName: 'Ladson',
      address: '3016 Loxley Lane Ladson, CA, 90210',
      city: 'Loxley Lane',
      state: 'CA',
      zip: '90210',
      contactName: 'Seth Ling',
      phone: '555-555',
      ext: '555',
      email: 'mail@mail.com'
    },
    {
      companyName: 'SonLad',
      address: '3016 Loxley Lane Ladson, CA, 90210',
      city: 'Loxley Lane',
      state: 'CA',
      zip: '90210',
      contactName: 'Seth Ling',
      phone: '555-555',
      ext: '555',
      email: 'mail@mail.com'
    }
  ]
}
