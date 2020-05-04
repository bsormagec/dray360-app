import { pools } from '@/views/Details/inner_types'

export const getFieldLocation = ({ pool, poolType, fieldName }) => {
  if (poolType === pools.form) {
    return searchInForm({ pool, fieldName })
  } else if (poolType === pools.document) {
    return searchInDocument({ pool, fieldName })
  }
}

function searchInForm ({ pool, fieldName }) {
  let location
  try {
    for (const sectionKey in pool.sections) {
      for (const rootFieldKey in pool.sections[sectionKey].rootFields) {
        if (rootFieldKey === fieldName) {
          location = `${sectionKey}/rootFields/${fieldName}`
          throw new Error()
        }
      }

      for (const subSectionKey in pool.sections[sectionKey].subSections) {
        for (const fieldKey in pool.sections[sectionKey].subSections[subSectionKey].fields) {
          if (fieldKey === fieldName) {
            location = `${sectionKey}/subSections/${subSectionKey}/fields/${fieldName}`
            throw new Error()
          }
        }
      }
    }
  } catch (e) {
    return location
  }
}

function searchInDocument ({ pool, fieldName }) {
  let location
  try {
    pool.forEach((page, pageIndex) => {
      page.highlights.forEach((highlight, hIndex) => {
        if (highlight.field === fieldName) {
          location = `${pageIndex}/highlights/${hIndex}`
          throw new Error()
        }
      })
    })
  } catch (e) {
    return location
  }
}
