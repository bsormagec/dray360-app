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

export const parse = ({ data, valSetter }) => {
  const ocrData = data.ocr_data

  const parsed = [
  ]

  for (const imgKey in ocrData.page_index_filenames.value) {
    parsed.push({
      image: ocrData.page_index_filenames.value[imgKey].presigned_download_uri,
      highlights: getHighlights(imgKey, data, valSetter)
    })
  }

  return parsed
}

function getHighlights (id, data, valSetter) {
  const ocrData = data.ocr_data

  const highlights = Object.keys(ocrData.fields).map(fieldKey => {
    if (!ocrData.fields[fieldKey].ocr_region) return

    const matches = ocrData.fields[fieldKey].ocr_region.page_index === parseInt(id)

    if (matches) {
      const { bottom, left, right, top } = ocrData.fields[fieldKey].ocr_region

      return {
        bottom,
        left,
        right,
        top,
        name: mapFieldNames(
          ocrData.fields[fieldKey].name
        ),
        ...valSetter({
          dray360name: ocrData.fields[fieldKey].d360_name,
          data
        })
      }
    }
  }).filter(v => Boolean(v))

  return highlights
}
