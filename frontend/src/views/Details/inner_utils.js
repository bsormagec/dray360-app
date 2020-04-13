export const exampleForm = {
  sections: [
    {
      title: 'shipment',
      rootFields: [
        {
          name: 'shipment designation',
          value: 'international'
        },
        {
          name: 'shipment direction',
          value: 'import'
        },
        {
          name: 'shipment handling',
          value: 'drop & hook'
        },
        {
          name: 'one way',
          value: 'yes'
        },
        {
          name: 'expedite shipment',
          value: 'no'
        },
        {
          name: 'hazardous',
          value: 'yes'
        }
      ],
      subSections: [
        {
          title: 'equipment information',
          fields: [
            {
              name: 'type',
              value: 'container'
            },
            {
              name: 'unit number',
              value: 'AAAU6578953'
            },
            {
              name: 'equipment',
              value: 'GP-General Purpose'
            },
            {
              name: 'size',
              value: '20 ft'
            },
            {
              name: 'yard pre-pull',
              value: 'yes'
            },
            {
              name: 'has chassis',
              value: 'no'
            },
            {
              name: 'owner or SS company',
              value: 'ACL'
            }
          ]
        },
        {
          title: 'shipment origin',
          fields: [
            {
              name: 'reference number',
              value: '13698454-54'
            },
            {
              name: 'rate quote number',
              value: '456466'
            },
            {
              name: 'port/ramp of origin',
              value: 'GLOBAL 4 IL'
            },
            {
              name: 'port/ramp of destination',
              value: 'CHIFOUSEV IL'
            },
            {
              name: 'vessel',
              value: 'caribbean queen'
            },
            {
              name: 'voyage',
              value: '5134A'
            },
            {
              name: 'master BOL / MAWB',
              value: 'DFETR'
            },
            {
              name: 'expedite shipment',
              value: 'no'
            },
            {
              name: 'hazardous',
              value: 'yes'
            }
          ]
        },
        {
          title: 'billing information',
          fields: [
            {
              name: 'bill to',
              value: {
                type: 'link',
                href: '#',
                icon: 'mdi-account-box',
                text: 'CAI Logistics'
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
          title: '1: Pickup',
          fields: [
            {
              name: 'address',
              value: 'Watkins Manufacturing Corp'
            },
            {
              name: 'contact name',
              value: '--'
            },
            {
              name: 'phone',
              value: '(971) 256-5228'
            },
            {
              name: 'email',
              value: '--'
            },
            {
              name: 'notes',
              value: 'Pickup Apt Req. Online'
            }
          ]
        }
      ]
    },
    {
      title: 'inventory',
      subSections: [
        {
          title: 'item 1',
          fields: [
            {
              name: 'quantity',
              value: '2000 bags'
            },
            {
              name: 'description',
              value: 'Chicken Feed'
            },
            {
              name: 'weight/unit',
              value: '40 lbs'
            },
            {
              name: 'total weight',
              value: '80,000 lbs'
            },
            {
              name: 'hazardous',
              value: 'no'
            }
          ]
        }
      ]
    },
    {
      title: 'notes',
      rootFields: [
        {
          type: 'text',
          text: `
          1. Carrier, you are hereby advised that some frieght tendered to you may contain a GPS tracking device.
          2. Carrier agrees to assume full liability for any fines imposed on us or the beneficial owner for the carrier’s failure to maintain all applicable operation permits, certificates and licenses under federal and state laws and regulations
          `
        }
      ]
    }
  ]
}
