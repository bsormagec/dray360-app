export const exampleForm = {
  sections: [
    {
      title: 'shipment',
      rootFields: [
        buildField({
          name: 'shipment designation',
          type: 'input',
          placeholder: 'international'
        }),
        buildField({
          name: 'shipment direction',
          type: 'select',
          options: ['a', 'b', 'c']
        }),
        buildField({
          name: 'shipment handling',
          type: 'select',
          options: ['a', 'b', 'c']
        }),
        buildField({
          name: 'one way',
          type: 'switch'
        }),
        buildField({
          name: 'expedite shipment',
          type: 'switch'
        }),
        buildField({
          name: 'hazardous',
          type: 'switch'
        })
      ],
      subSections: [
        {
          title: 'equipment',
          fields: [
            buildField({
              name: 'type',
              type: 'input',
              placeholder: 'container'
            }),
            buildField({
              name: 'unit number',
              type: 'input',
              placeholder: 'AAAU656578'
            }),
            buildField({
              name: 'equipment',
              type: 'input',
              placeholder: 'GP-General Purpose'
            }),
            buildField({
              name: 'size',
              type: 'input',
              placeholder: '20 ft'
            }),
            buildField({
              name: 'yard pre-pull',
              type: 'switch'
            }),
            buildField({
              name: 'has chassis',
              type: 'switch'
            }),
            buildField({
              name: 'owner or SS company',
              type: 'input',
              placeholder: 'ACL'
            })
          ]
        },
        {
          title: 'origin',
          fields: [
            buildField({
              name: 'reference number',
              type: 'input',
              placeholder: 'reference number'
            }),
            buildField({
              name: 'rate quote number',
              type: 'input',
              placeholder: 'rate quote number'
            }),
            buildField({
              name: 'port/ramp of origin',
              type: 'input',
              placeholder: 'port/ramp of origin'
            }),
            buildField({
              name: 'port/ramp of destination',
              type: 'input',
              placeholder: 'port/ramp of destination'
            }),
            buildField({
              name: 'vessel',
              type: 'input',
              placeholder: 'vessel'
            }),
            buildField({
              name: 'voyage',
              type: 'input',
              placeholder: 'voyage'
            }),
            buildField({
              name: 'master BOL / MAWB',
              type: 'input',
              placeholder: 'master BOL / MAWB'
            }),
            buildField({
              name: 'house BOL / MAWB',
              type: 'input',
              placeholder: 'house BOL / MAWB'
            }),
            buildField({
              name: '(Est) arrival',
              type: 'date-time'
            }),
            buildField({
              name: 'last free day',
              type: 'date-time'
            })
          ]
        },
        {
          title: 'billing',
          fields: [
            buildField({
              name: 'bill to',
              type: 'modal-select',
              placeholder: 'select address',
              options: addresses()
            }),
            buildField({
              name: 'company name',
              type: 'input',
              placeholder: 'company name'
            }),
            buildField({
              name: 'address',
              type: 'text-area',
              placeholder: 'address'
            }),
            buildField({
              name: 'city',
              type: 'input',
              placeholder: 'city'
            }),
            buildField({
              name: 'state',
              type: 'input',
              placeholder: 'state'
            }),
            buildField({
              name: 'zip',
              type: 'input',
              placeholder: 'zip'
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
              name: 'ext',
              type: 'input',
              placeholder: 'ext'
            }),
            buildField({
              name: 'email',
              type: 'input',
              placeholder: 'email'
            })
          ]
        }
      ]
    },
    {
      title: 'itinerary',
      subSections: [
        {
          title: 'hook: rail or port terminal',
          fields: [
            buildField({
              name: 'hook',
              type: 'modal-select',
              placeholder: 'select address',
              options: addresses()
            }),
            buildField({
              name: 'company name',
              type: 'input',
              placeholder: 'company name'
            }),
            buildField({
              name: 'address',
              type: 'text-area',
              placeholder: 'address'
            }),
            buildField({
              name: 'city',
              type: 'input',
              placeholder: 'city'
            }),
            buildField({
              name: 'state',
              type: 'input',
              placeholder: 'state'
            }),
            buildField({
              name: 'zip',
              type: 'input',
              placeholder: 'zip'
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
              name: 'ext',
              type: 'input',
              placeholder: 'ext'
            }),
            buildField({
              name: 'email',
              type: 'input',
              placeholder: 'email'
            }),
            buildField({
              name: 'notes',
              type: 'text-area',
              placeholder: 'notes'
            }),
            buildField({
              name: 'hours',
              type: 'input',
              placeholder: 'hours'
            })
          ]
        },
        {
          title: 'deliver: container to',
          fields: [
            buildField({
              name: 'deliver',
              type: 'modal-select',
              placeholder: 'select address',
              options: addresses()
            }),
            buildField({
              name: 'company name',
              type: 'input',
              placeholder: 'company name'
            }),
            buildField({
              name: 'address',
              type: 'text-area',
              placeholder: 'address'
            }),
            buildField({
              name: 'city',
              type: 'input',
              placeholder: 'city'
            }),
            buildField({
              name: 'state',
              type: 'input',
              placeholder: 'state'
            }),
            buildField({
              name: 'zip',
              type: 'input',
              placeholder: 'zip'
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
              name: 'ext',
              type: 'input',
              placeholder: 'ext'
            }),
            buildField({
              name: 'email',
              type: 'input',
              placeholder: 'email'
            }),
            buildField({
              name: 'notes',
              type: 'text-area',
              placeholder: 'notes'
            }),
            buildField({
              name: 'hours',
              type: 'input',
              placeholder: 'hours'
            }),
            buildField({
              name: 'delivery instructions',
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
          ]
        },
        {
          title: 'dismount: return empty to depot',
          fields: [
            buildField({
              name: 'dismount',
              placeholder: 'select address',
              type: 'modal-select',
              options: addresses()
            }),
            buildField({
              name: 'company name',
              type: 'input',
              placeholder: 'company name'
            }),
            buildField({
              name: 'address',
              type: 'text-area',
              placeholder: 'address'
            }),
            buildField({
              name: 'city',
              type: 'input',
              placeholder: 'city'
            }),
            buildField({
              name: 'state',
              type: 'input',
              placeholder: 'state'
            }),
            buildField({
              name: 'zip',
              type: 'input',
              placeholder: 'zip'
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
              name: 'ext',
              type: 'input',
              placeholder: 'ext'
            }),
            buildField({
              name: 'email',
              type: 'input',
              placeholder: 'email'
            }),
            buildField({
              name: 'notes',
              type: 'text-area',
              placeholder: 'notes'
            }),
            buildField({
              name: 'hours',
              type: 'input',
              placeholder: 'hours'
            }),
            buildField({
              name: 'pickup instructions',
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
          ]
        }
      ]
    },
    {
      title: 'inventory',
      rootFields: [
        buildField({
          name: 'quantity',
          type: 'input',
          placeholder: 'quantity'
        }),
        buildField({
          name: 'unit of measure',
          type: 'select',
          options: ['a', 'b', 'c']
        }),
        buildField({
          name: 'description',
          type: 'input',
          placeholder: 'description'
        }),
        buildField({
          name: 'weight/unit',
          type: 'input-select',
          options: ['a', 'b', 'c']
        }),
        buildField({
          name: 'total weight',
          type: 'input',
          placeholder: 'total weight'
        }),
        buildField({
          name: 'hazardous',
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
      ]
    },
    {
      title: 'notes',
      rootFields: [
        buildField({
          name: 'notes',
          type: 'text-area',
          placeholder: 'notes'
        })
      ]
    }
  ]
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
