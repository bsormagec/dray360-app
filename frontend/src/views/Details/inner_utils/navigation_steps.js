import { detailsState } from '@/views/Details/inner_store'

export const navigationSteps = () => {
  const steps = []
  let count = 0

  Object.keys(detailsState.form.sections).forEach(sectionKey => {
    count += 1
    steps.push({
      text: sectionKey,
      id: count
    })

    if (detailsState.form.sections[sectionKey].subSections) {
      Object.keys(detailsState.form.sections[sectionKey].subSections).forEach((subSectionKey, subSectIndex) => {
        count += 0.1
        count = parseFloat(count.toFixed(1))
        steps.push({
          text: subSectionKey,
          id: count,
          isInventoryItem: sectionKey === 'inventory',
          inventoryItemText: `Item ${subSectIndex + 1}`
        })
      })
      count = String(count).split('.')
      count = Number(count[0])
    }
  })

  return steps
}
