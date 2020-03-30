import { getCsrfCookie, postLogin } from '@/store/api_calls/auth'

export default async () => {
  const credentials = {
    password: 'mongomongo',
    email: 'peter+test13@peternelson.com',
    name: 'test user'
  }

  const [cookieError] = await getCsrfCookie()
  const [loginError] = await postLogin(credentials)

  const errors = [
    cookieError,
    loginError
  ]

  errors.forEach(e => {
    if (e) console.log(JSON.stringify(e, null, 2))
  })
}
