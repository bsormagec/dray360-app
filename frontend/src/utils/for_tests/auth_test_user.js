import { getCsrfCookie, postLogin } from '@/store/api_calls/auth'

export default async () => {
  const credentials = {
    password: process.env.VUE_APP_TEST_USER_PASSWORD,
    email: process.env.VUE_APP_TEST_USER_EMAIL,
    name: process.env.VUE_APP_TEST_USER_NAME
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
