export const getFieldLocation = ({ form, fieldName }) => {
  let location

  try {
    for (const sectionKey in form.sections) {
      for (const rootFieldKey in form.sections[sectionKey].rootFields) {
        if (rootFieldKey === fieldName) {
          location = `${sectionKey}/rootFields/${fieldName}`
          throw new Error()
        }
      }

      for (const subSectionKey in form.sections[sectionKey].subSections) {
        for (const fieldKey in form.sections[sectionKey].subSections[subSectionKey].fields) {
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
