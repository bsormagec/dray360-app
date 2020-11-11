export const getVariantTypeFromFile = (file) => {
  switch (file.type) {
    case 'text/csv':
    case 'application/wps-office.xlsx':
    case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
      return 'tabular'
    case '':
    case 'text/plain':
    case 'application/EDI-X12':
    case 'application/EDIFACT':
    case 'application/EDI-consent':
      return 'edi'
    default:
      return 'ocr'
  }
}

export const isPdf = (file) => file.type === 'application/pdf'
