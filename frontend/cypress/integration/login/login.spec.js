describe('The Login Page', function () {
  beforeEach(() => {
    cy.fixture('auth/credentials.json').as('credentials')
    cy.fixture('auth/tenant.json').as('tenant')
    cy.fixture('auth/user.json').as('user')
  })

  it('logs in successfully', function () {
    cy.server()

    cy.route({
      url: '**/api/user',
      status: 401,
      response: { message: 'Unauthenticated.' }
    })
    cy.route({ url: '**/api/current-tenant', response: this.tenant })
    cy.route({
      url: '**/sanctum/csrf-cookie',
      status: 204,
      response: ''
    })
    cy.route({
      method: 'POST',
      url: '**/api/login',
      status: 204,
      response: ''
    })

    cy.visit('localhost:8080')
    cy.get('input[name=username]').type(this.credentials.email, { force: true })
    cy.get('input[name=password]').type(this.credentials.password, { force: true })

    cy.route({ url: '**/api/user', response: this.user })
    cy.route({ url: '**/api/orders**', response: {} })

    cy.get('[type=button]').click({ multiple: true, force: true })

    cy.url().should('include', '/inbox')
  })
})
