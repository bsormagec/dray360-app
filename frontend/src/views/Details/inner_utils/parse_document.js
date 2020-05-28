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
import { defaultsTo } from '@/utils/defaults_to'
import { uuid } from '@/utils/uuid_valid_id'
import mapFieldNames from '@/views/Details/inner_utils/map_field_names'

export const parse = ({ data, valSetter }) => {
  const ocrData = defaultsTo(() => data.ocr_data, {})

  const parsed = [
  ]

  for (const imgKey in defaultsTo(() => ocrData.page_index_filenames.value, '--')) {
    parsed.push({
      image: defaultsTo(() => ocrData.page_index_filenames.value[imgKey].presigned_download_uri, '--'),
      highlights: getHighlights(imgKey, data, valSetter)
    })
  }

  return parsed
}

function getHighlights (id, data, valSetter) {
  const ocrData = data.ocr_data
  const highlights = {}

  Object.keys(ocrData.fields).forEach(fieldKey => {
    if (!defaultsTo(() => ocrData.fields[fieldKey].ocr_region, false)) return
    const matches = defaultsTo(() => ocrData.fields[fieldKey].ocr_region.page_index, 0) === parseInt(id)

    if (matches) {
      const parsedKey = fieldKey.includes('event') ? fieldKey.split('_')[0] : fieldKey
      const { bottom, left, right, top } = defaultsTo(() => ocrData.fields[fieldKey].ocr_region, {})

      const hData = (editions = {}) => ({
        bottom: editions.bottom || bottom,
        left: editions.left || left,
        right: editions.right || right,
        top: editions.top || top,
        name: mapFieldNames(
          defaultsTo(() => ocrData.fields[fieldKey].name, '--')
        ),
        ...valSetter({
          dray360name: defaultsTo(() => ocrData.fields[fieldKey].d360_name, uuid()),
          data
        })
      })

      if (highlights[parsedKey]) {
        highlights[parsedKey] = hData({
          bottom: bottom >= highlights[parsedKey].bottom ? bottom : highlights[parsedKey].bottom,
          left: left <= highlights[parsedKey].left ? left : highlights[parsedKey].left,
          right: right >= highlights[parsedKey].right ? right : highlights[parsedKey].right,
          top: top <= highlights[parsedKey].top ? top : highlights[parsedKey].top
        })
      } else {
        highlights[parsedKey] = hData()
      }
    }
  })

  return Object.values(highlights)
}
