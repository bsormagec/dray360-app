export default ({ length, from = 0 }) => {
  const numbers = []

  for (let i = from; i <= length; i++) {
    numbers.push(i)
  }

  return numbers
}
