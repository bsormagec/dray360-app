import format from 'date-fns/format'

export const formatDate = (stringDate) => {
  return format(new Date(stringDate), 'MM/dd/yyyy HH:mm')
}
