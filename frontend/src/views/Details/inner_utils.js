export const exampleForm = {
  sections: [
    {
      title: 'shipment',
      rootFields: [
        {
          name: 'shipment designation',
          el: {
            type: 'input',
            placeholder: 'international'
          }
        },
        {
          name: 'shipment direction',
          el: {
            type: 'select',
            options: ['a', 'b', 'c']
          }
        },
        {
          name: 'shipment handling',
          el: {
            type: 'select',
            options: ['a', 'b', 'c']
          }
        },
        {
          name: 'one way',
          el: {
            type: 'switch'
          }
        },
        {
          name: 'expedite shipment',
          el: {
            type: 'switch'
          }
        },
        {
          name: 'hazardous',
          el: {
            type: 'switch'
          }
        }
      ],
      subSections: [
        {
          title: 'equipment',
          fields: [
            {
              name: 'type',
              el: {
                type: 'input',
                placeholder: 'container'
              }
            },
            {
              name: 'unit number',
              el: {
                type: 'input',
                placeholder: 'AAAU656578'
              }
            },
            {
              name: 'equipment',
              el: {
                type: 'input',
                placeholder: 'GP-General Purpose'
              }
            },
            {
              name: 'size',
              el: {
                type: 'input',
                placeholder: '20 ft'
              }
            },
            {
              name: 'yard pre-pull',
              el: {
                type: 'switch'
              }
            },
            {
              name: 'has chassis',
              el: {
                type: 'switch'
              }
            },
            {
              name: 'owner or SS company',
              el: {
                type: 'input',
                placeholder: 'ACL'
              }
            }
          ]
        },
        {
          title: 'origin',
          fields: [
            {
              name: 'reference number',
              el: {
                type: 'input'
              }
            },
            {
              name: 'rate quote number',
              el: {
                type: 'input'
              }
            },
            {
              name: 'port/ramp of origin',
              el: {
                type: 'input'
              }
            },
            {
              name: 'port/ramp of destination',
              el: {
                type: 'input'
              }
            },
            {
              name: 'vessel',
              el: {
                type: 'input'
              }
            },
            {
              name: 'voyage',
              el: {
                type: 'input'
              }
            },
            {
              name: 'master BOL / MAWB',
              el: {
                type: 'input'
              }
            },
            {
              name: 'house BOL / MAWB',
              el: {
                type: 'input'
              }
            },
            {
              name: '(Est) arrival',
              el: {
                type: 'date-time'
              }
            },
            {
              name: 'last free day',
              el: {
                type: 'date-time'
              }
            }
          ]
        },
        {
          title: 'billing',
          fields: [
            {
              name: 'bill to',
              // value: {
              //   type: 'link',
              //   href: '#',
              //   icon: 'mdi-account-box',
              //   text: 'CAI Logistics'
              // },
              el: {
                placeholder: 'select address',
                type: 'modal-select',
                options: [
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
            },
            {
              name: 'company name',
              el: {
                type: 'input'
              }
            },
            {
              name: 'address',
              el: {
                type: 'text-area'
              }
            },
            {
              name: 'city',
              el: {
                type: 'input'
              }
            },
            {
              name: 'state',
              el: {
                type: 'input'
              }
            },
            {
              name: 'zip',
              el: {
                type: 'input'
              }
            },
            {
              name: 'contact name',
              el: {
                type: 'input'
              }
            },
            {
              name: 'phone',
              el: {
                type: 'input'
              }
            },
            {
              name: 'ext',
              el: {
                type: 'input'
              }
            },
            {
              name: 'email',
              el: {
                type: 'input'
              }
            }
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
            {
              name: 'company name',
              el: {
                type: 'input'
              }
            },
            {
              name: 'address',
              el: {
                type: 'text-area'
              }
            },
            {
              name: 'city',
              el: {
                type: 'input'
              }
            },
            {
              name: 'state',
              el: {
                type: 'input'
              }
            },
            {
              name: 'zip',
              el: {
                type: 'input'
              }
            },
            {
              name: 'contact name',
              el: {
                type: 'input'
              }
            },
            {
              name: 'phone',
              el: {
                type: 'input'
              }
            },
            {
              name: 'ext',
              el: {
                type: 'input'
              }
            },
            {
              name: 'email',
              el: {
                type: 'input'
              }
            },
            {
              name: 'notes',
              el: {
                type: 'text-area'
              }
            },
            {
              name: 'hours',
              el: {
                type: 'input'
              }
            }
          ]
        },
        {
          title: 'deliver: container to',
          fields: [
            {
              name: 'company name',
              el: {
                type: 'input'
              }
            },
            {
              name: 'address',
              el: {
                type: 'text-area'
              }
            },
            {
              name: 'city',
              el: {
                type: 'input'
              }
            },
            {
              name: 'state',
              el: {
                type: 'input'
              }
            },
            {
              name: 'zip',
              el: {
                type: 'input'
              }
            },
            {
              name: 'contact name',
              el: {
                type: 'input'
              }
            },
            {
              name: 'phone',
              el: {
                type: 'input'
              }
            },
            {
              name: 'ext',
              el: {
                type: 'input'
              }
            },
            {
              name: 'email',
              el: {
                type: 'input'
              }
            },
            {
              name: 'notes',
              el: {
                type: 'text-area'
              }
            },
            {
              name: 'hours',
              el: {
                type: 'input'
              }
            },
            {
              el: {
                name: 'delivery instructions',
                type: 'radio',
                options: [
                  'call for appointment',
                  {
                    name: 'deliver between',
                    el: [
                      { type: 'date-time' },
                      { placeholder: 'delivery instructions', type: 'text-area' }
                    ]
                  }
                ]
              }
            }
          ]
        },
        {
          title: 'dismount: return empty to depot',
          fields: [
            {
              name: 'company name',
              el: {
                type: 'input'
              }
            },
            {
              name: 'address',
              el: {
                type: 'text-area'
              }
            },
            {
              name: 'city',
              el: {
                type: 'input'
              }
            },
            {
              name: 'state',
              el: {
                type: 'input'
              }
            },
            {
              name: 'zip',
              el: {
                type: 'input'
              }
            },
            {
              name: 'contact name',
              el: {
                type: 'input'
              }
            },
            {
              name: 'phone',
              el: {
                type: 'input'
              }
            },
            {
              name: 'ext',
              el: {
                type: 'input'
              }
            },
            {
              name: 'email',
              el: {
                type: 'input'
              }
            },
            {
              name: 'notes',
              el: {
                type: 'text-area'
              }
            },
            {
              name: 'hours',
              el: {
                type: 'input'
              }
            },
            {
              el: {
                name: 'pickup instructions',
                type: 'radio',
                options: [
                  'call for appointment',
                  {
                    name: 'deliver between',
                    el: [
                      { type: 'date-time' },
                      { placeholder: 'pickup instructions', type: 'text-area' }
                    ]
                  }
                ]
              }
            }
          ]
        }
      ]
    },
    {
      title: 'inventory',
      rootFields: [
        {
          name: 'quantity',
          el: {
            type: 'input'
          }
        },
        {
          name: 'unit of measure',
          el: {
            type: 'select',
            options: ['a', 'b', 'c']
          }
        },
        {
          name: 'description',
          el: {
            type: 'input'
          }
        },
        {
          name: 'weight/unit',
          el: {
            type: 'input-select',
            options: ['a', 'b', 'c']
          }
        },
        {
          name: 'total weight',
          el: {
            type: 'input'
          }
        },
        {
          name: 'hazardous',
          el: {
            type: 'switch',
            switchActiveContent: {
              title: 'hazardous item information',
              fields: [
                {
                  name: 'contact name',
                  el: {
                    type: 'input'
                  }
                },
                {
                  name: 'phone',
                  el: {
                    type: 'input'
                  }
                },
                {
                  name: 'UN code',
                  el: {
                    type: 'input'
                  }
                },
                {
                  name: 'qualifier',
                  el: {
                    type: 'input',
                    prepend: 'C'
                  }
                },
                {
                  name: 'flashpoint temp',
                  el: {
                    type: 'input'
                  }
                },
                {
                  name: 'UN name',
                  el: {
                    type: 'input'
                  }
                },
                {
                  name: 'HAZ class',
                  el: {
                    type: 'input'
                  }
                },
                {
                  name: 'IMDG page no',
                  el: {
                    type: 'input'
                  }
                },
                {
                  name: 'packaging group',
                  el: {
                    type: 'input'
                  }
                },
                {
                  name: 'decription',
                  el: {
                    type: 'textarea'
                  }
                }
              ]
            }
          }
        }
      ]
    },
    {
      title: 'notes',
      rootFields: [
        {
          name: 'notes',
          el: {
            type: 'textarea'
          }
        }
      ]
    }
  ]
}

export const navigationSteps = () => {
  const steps = []
  let count = 0

  exampleForm.sections.forEach(section => {
    count += 1
    steps.push({
      text: section.title,
      id: count
    })

    if (section.subSections) {
      section.subSections.forEach(subSection => {
        count += 0.1
        count = parseFloat(count.toFixed(1))
        steps.push({
          text: subSection.title,
          id: count
        })
      })
      count = String(count).split('.')
      count = Number(count[0])
    }
  })

  return steps
}
