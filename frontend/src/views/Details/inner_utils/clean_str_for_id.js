export const cleanStrForId = (str) => str ? str.toLowerCase().replace(/[^\w\s]/gi, '').replace(/\s+/g, '') : ''
