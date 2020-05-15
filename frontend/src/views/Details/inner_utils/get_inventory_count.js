export const getInventoryCount = (form) => {
  const inventorySubSections = form.sections.inventory.subSections
  const inventoryItemsCount = Object.keys(inventorySubSections).length
  let hazardousCount = 0

  for (const key in inventorySubSections) {
    if (inventorySubSections[key].fields.hazardous.value === 'yes') {
      hazardousCount += 1
    }
  }

  return {
    inventoryItemsCount,
    hazardousCount
  }
}
