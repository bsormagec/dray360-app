// import { uuid } from '@/utils/uuid_valid_id'

export const exampleForm = {
  sections: {
    shipment: {
      rootFields: {
        'shipment designation': buildField({
          type: 'input',
          placeholder: 'international'
        }),
        'shipment direction': buildField({
          type: 'text-area',
          placeholder: 'shipment address'
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
          type: 'switch',
          children: hazardousFields()
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
            vessel: buildField({
              type: 'input',
              placeholder: 'vessel'
            }),
            voyage: buildField({
              type: 'input',
              placeholder: 'voyage'
            }),
            'master BOL MAWB': buildField({
              type: 'input',
              placeholder: 'master BOL / MAWB'
            }),
            'house BOL MAWB': buildField({
              type: 'input',
              placeholder: 'house BOL / MAWB'
            }),
            '(Est) arrival': buildField({
              type: 'date-time'
            }),
            'last free day': buildField({
              type: 'date-time'
            })
            // 'port ramp of origin': buildField({
            //   type: 'modal-address',
            //   placeholder: 'port/ramp of origin'
            // }),
            // 'port ramp of destination': buildField({
            //   type: 'modal-address',
            //   placeholder: 'port/ramp of destination'
            // })
          }
        },
        billing: {
          fields: {
            // 'bill to': buildField({
            //   type: 'modal-select',
            //   placeholder: 'select address',
            //   options: addresses()
            // })
            'bill to': buildField({
              type: 'modal-address',
              isEditing: true,
              readonly: false
            })
          }
        }
      }
    },
    itinerary: {
      rootFields: {}
      // subSections: {
      //   'hook: rail or port terminal': {
      //     fields: {
      //       // hook: buildField({
      //       //   type: 'modal-select',
      //       //   placeholder: 'select address',
      //       //   options: addresses()
      //       // })
      //       hook: buildField({
      //         type: 'text-area',
      //         placeholder: 'hook address'
      //       })
      //     }
      //   },
      //   'deliver: container to': {
      //     fields: {
      //       // deliver: buildField({
      //       //   type: 'modal-select',
      //       //   placeholder: 'select address',
      //       //   options: addresses()
      //       // }),
      //       deliver: buildField({
      //         type: 'text-area',
      //         placeholder: 'deliver address'
      //       }),
      //       'delivery instructions': buildField({
      //         type: 'radio',
      //         options: {
      //           'call for appointment': buildField({}),
      //           'deliver between': buildField({
      //             children: {
      //               start: buildField({ type: 'time', width: '48%' }),
      //               end: buildField({ type: 'time', width: '48%' }),
      //               instructions: buildField({
      //                 type: 'text-area',
      //                 width: '100%',
      //                 placeholder: 'delivery instructions'
      //               })
      //             }
      //           })
      //         }
      //       })
      //     }
      //   },
      //   'dismount: return empty to depot': {
      //     fields: {
      //       // dismount: buildField({
      //       //   placeholder: 'select address',
      //       //   type: 'modal-select',
      //       //   options: addresses()
      //       // }),
      //       dismount: buildField({
      //         type: 'text-area',
      //         placeholder: 'dismount address'
      //       }),
      //       'pickup instructions': buildField({
      //         type: 'radio',
      //         options: {
      //           'call for appointment': buildField({}),
      //           'pickup on': buildField({
      //             children: {
      //               date: buildField({ type: 'date' }),
      //               between: buildField({ type: 'label', width: '100%' }),
      //               start: buildField({ type: 'time', width: '48%' }),
      //               end: buildField({ type: 'time', width: '48%' }),
      //               instructions: buildField({
      //                 type: 'text-area',
      //                 width: '100%',
      //                 placeholder: 'delivery instructions'
      //               })
      //             }
      //           })
      //         }
      //       })
      //     }
      //   }
      // }
    },
    inventory: {
      subSections: {
        // [uuid()]: {
        //   fields: inventoryItemFields()
        // }
      },
      actionSection: buildField({ type: 'add-inventory-item' })
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
  verified
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

// function addresses () {
//   return {
//     preselected: [
//       {
//         'company name': 'Ladson',
//         address: '3016 Loxley Lane Ladson, CA, 90210',
//         city: 'Loxley Lane',
//         state: 'CA',
//         zip: '90210',
//         'contact name': 'Seth Ling',
//         phone: '555-555',
//         ext: '555',
//         email: 'mail@mail.com'
//       },
//       {
//         'company name': 'SonLad',
//         address: '3016 Loxley Lane Ladson, CA, 90210',
//         city: 'Loxley Lane',
//         state: 'CA',
//         zip: '90210',
//         'contact name': 'Seth Ling',
//         phone: '555-555',
//         ext: '555',
//         email: 'mail@mail.com'
//       }
//     ],
//     fields: {
//       'company name': buildField({
//         type: 'input',
//         placeholder: 'company name'
//       }),
//       address: buildField({
//         type: 'text-area',
//         placeholder: 'address'
//       }),
//       city: buildField({
//         type: 'input',
//         placeholder: 'city'
//       }),
//       state: buildField({
//         type: 'input',
//         placeholder: 'state'
//       }),
//       zip: buildField({
//         type: 'input',
//         placeholder: 'zip'
//       }),
//       'contact name': buildField({
//         type: 'input',
//         placeholder: 'contact name'
//       }),
//       phone: buildField({
//         type: 'input',
//         placeholder: 'phone'
//       }),
//       ext: buildField({
//         type: 'input',
//         placeholder: 'ext'
//       }),
//       email: buildField({
//         type: 'input',
//         placeholder: 'email'
//       }),
//       notes: buildField({
//         type: 'text-area',
//         placeholder: 'notes'
//       }),
//       hours: buildField({
//         type: 'input',
//         placeholder: 'hours'
//       })
//     }
//   }
// }

function hazardousFields () {
  return {
    'hazardous item information': buildField({
      type: 'info-title'
    }),
    'contact name': buildField({
      type: 'input',
      placeholder: 'contact name'
    }),
    phone: buildField({
      type: 'input',
      placeholder: 'phone'
    }),
    'UN code': buildField({
      type: 'input',
      placeholder: 'UN code'
    }),
    qualifier: buildField({
      type: 'input',
      placeholder: 'qualifier'
    }),
    'flashpoint temp': buildField({
      type: 'input',
      placeholder: 'flashpoint temp'
    }),
    'UN name': buildField({
      type: 'input',
      placeholder: 'UN name'
    }),
    'HAZ class': buildField({
      type: 'input',
      placeholder: 'HAZ class'
    }),
    'IMDG page no': buildField({
      type: 'input',
      placeholder: 'IMDG page no'
    }),
    'packaging group': buildField({
      type: 'input',
      placeholder: 'packaging group'
    }),
    description: buildField({
      type: 'text-area',
      placeholder: 'description'
    })
  }
}

export function inventoryItemFields () {
  return {
    // quantity: buildField({
    //   type: 'input',
    //   placeholder: 'quantity'
    // }),
    // 'unit of measure': buildField({
    //   type: 'select',
    //   options: ['a', 'b', 'c']
    // }),
    // description: buildField({
    //   type: 'input',
    //   placeholder: 'description'
    // }),
    // 'weight unit': buildField({
    //   type: 'input-select',
    //   options: ['a', 'b', 'c']
    // }),
    // 'total weight': buildField({
    //   type: 'input',
    //   placeholder: 'total weight'
    // }),
    description: buildField({
      type: 'text-area',
      placeholder: 'description'
    })
    // hazardous: buildField({
    //   type: 'switch',
    //   children: hazardousFields()
    // })
  }
}
