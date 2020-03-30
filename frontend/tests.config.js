require('dotenv').config({ path: '.env.test' })
const jest = require('jest')

process.env.BABEL_ENV = 'test'
process.env.NODE_ENV = 'test'

jest.run('--watchAll')
