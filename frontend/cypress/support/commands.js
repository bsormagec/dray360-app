Cypress.Commands.add('login', (email, password, tenant, user) => {
  cy.server()

  cy.route({
    url: '**/api/user',
    status: 401,
    response: { message: 'Unauthenticated.' }
  })
  cy.route({ url: '**/api/current-tenant', response: tenant })
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
  cy.get('input[name=username]').type(email)
  cy.get('input[name=password]').type(password)
  cy.get('[type=button]').click()

  cy.route({ url: '**/api/user', response: user })
  cy.route({ url: '**/api/orders**', response: {} })
})
