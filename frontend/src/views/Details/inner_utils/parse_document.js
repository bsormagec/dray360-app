/*
  [
    {
      image: "url"
      highlights: [
        {
          bottom: 0,
          left: 0,
          right: 0,
          top: 0,
          name: 'name',
          value: 'value'
        }
      ]
    }
  ]
*/

import exampleDocument from '@/views/Details/inner_utils/example_document'
import mapFieldNames from '@/views/Details/inner_utils/map_field_names'

const parse = () => {
  const parsed = [
  ]

  for (const imgKey in exampleDocument.page_index_filenames.value) {
    parsed.push({
      image: exampleDocument.page_index_filenames.value[imgKey].value,
      highlights: getHighlights(imgKey)
    })
  }

  return parsed
}

function getHighlights (id) {
  return Object.keys(exampleDocument.fields).map(fieldKey => {
    if (!exampleDocument.fields[fieldKey].ocr_region) return

    const matches = exampleDocument.fields[fieldKey].ocr_region.page_index === parseInt(id)
    if (matches) {
      const { bottom, left, right, top } = exampleDocument.fields[fieldKey].ocr_region

      return {
        bottom,
        left,
        right,
        top,
        name: mapFieldNames(exampleDocument.fields[fieldKey].name),
        value: exampleDocument.fields[fieldKey].value
      }
    }
  }).filter(v => Boolean(v))
}

export const parsedDocument = parse()
