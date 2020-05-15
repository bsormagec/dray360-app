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

// import exampleDocument from '@/views/Details/inner_utils/example_document'
import mapFieldNames from '@/views/Details/inner_utils/map_field_names'

export const parse = (d) => {
  const parsed = [
  ]

  for (const imgKey in d.page_index_filenames.value) {
    parsed.push({
      image: d.page_index_filenames.value[imgKey].value,
      highlights: getHighlights(imgKey, d)
    })
  }

  return parsed
}

function getHighlights (id, d) {
  const highlights = Object.keys(d.fields).map(fieldKey => {
    if (!d.fields[fieldKey].ocr_region) return

    const matches = d.fields[fieldKey].ocr_region.page_index === parseInt(id)
    if (matches) {
      const { bottom, left, right, top } = d.fields[fieldKey].ocr_region

      return {
        bottom,
        left,
        right,
        top,
        name: mapFieldNames(d.fields[fieldKey].name),
        value: d.fields[fieldKey].value
      }
    }
  }).filter(v => Boolean(v))

  return highlights
}
