import { getCsrfCookie, postLogin } from '@/store/api_calls/auth'

describe('app', () => {
  // This one may be updated to use the login page
  it('logs in', async () => {
    await getCsrfCookie()
    const res = await postLogin({ email: process.env.VUE_APP_TEST_USER_EMAIL, password: process.env.VUE_APP_TEST_USER_PASSWORD })
    expect(res[1].status).toBe(200)
  })
})
