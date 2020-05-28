export const defaultsTo = (valueCb = () => '--', valueDefault) => {
  if (typeof valueCb !== 'function') console.warn('valueCb should be a function')

  try {
    const val = valueCb()
    if (val === undefined || val === null) throw new Error()
    else return val
  } catch (e) {
    return valueDefault
  }
}
