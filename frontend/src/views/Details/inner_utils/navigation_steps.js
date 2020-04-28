import { exampleForm } from '@/views/Details/inner_utils/example_form.js'

export const navigationSteps = () => {
  const steps = []
  let count = 0

  Object.keys(exampleForm.sections).forEach(sectionKey => {
    count += 1
    steps.push({
      text: sectionKey,
      id: count
    })

    if (exampleForm.sections[sectionKey].subSections) {
      Object.keys(exampleForm.sections[sectionKey].subSections).forEach(subSectionKey => {
        count += 0.1
        count = parseFloat(count.toFixed(1))
        steps.push({
          text: subSectionKey,
          id: count
        })
      })
      count = String(count).split('.')
      count = Number(count[0])
    }
  })

  return steps
}
