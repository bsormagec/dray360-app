describe('Add user', function () {
  beforeEach(() => {
    cy.fixture('auth/tenant.json').as('tenant')
    cy.fixture('auth/customerAdmin.json').as('user')
    cy.fixture('user-mgmt/roles.json').as('roles')
    cy.fixture('user-mgmt/companies.json').as('companies')
    cy.fixture('user-mgmt/user-list.json').as('users')
    cy.fixture('user-mgmt/created-user.json').as('createdUser')
  })

  it('creates a user successfully', function () {
    cy.server()

    cy.route({ url: '**/api/user', response: this.user })

    cy.route({ url: '**/api/current-tenant', response: this.tenant })

    cy.route({ url: '**/api/roles', response: this.roles })

    cy.route({ url: '**/api/companies', response: this.companies })

    cy.visit('localhost:8080/user/dashboard/add-user')

    cy.get('[data-cy=name-input]').type('Bill Brasky', { force: true })

    cy.get('[data-cy=email-input]').type('bill@example.com', { force: true })

    cy.get('[data-cy=password-input]').type('mockedpass', { force: true })

    cy.route({ method: 'POST', url: '**/api/users', response: this.createdUser })

    cy.route({ method: 'GET', url: '**/api/users**', response: this.users })

    cy.route({ method: 'GET', url: '**/api/users', response: this.users })

    cy.get('[data-cy=save-button]').click({ force: true })

    cy.url().should('include', 'user/dashboard')
  })
})
