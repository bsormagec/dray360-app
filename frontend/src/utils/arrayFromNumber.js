export default ({ length, from }) => {
  const numbers = []

  for (let i = from; i <= length; i++) {
    numbers.push(i)
  }

  return numbers
}
