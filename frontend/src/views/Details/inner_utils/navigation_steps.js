import { exampleForm } from '@/views/Details/inner_utils/example_form.js'

export const navigationSteps = () => {
  const steps = []
  let count = 0

  exampleForm.sections.forEach(section => {
    count += 1
    steps.push({
      text: section.title,
      id: count
    })

    if (section.subSections) {
      section.subSections.forEach(subSection => {
        count += 0.1
        count = parseFloat(count.toFixed(1))
        steps.push({
          text: subSection.title,
          id: count
        })
      })
      count = String(count).split('.')
      count = Number(count[0])
    }
  })

  return steps
}
