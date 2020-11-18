describe('Edit profile', function () {
  beforeEach(() => {
    cy.fixture('auth/tenant.json').as('tenant')
    cy.fixture('auth/user.json').as('user')
    cy.fixture('user-mgmt/roles.json').as('roles')
    cy.fixture('user-mgmt/company.json').as('company')
    cy.fixture('user-mgmt/user-list.json').as('users')
    cy.fixture('user-mgmt/edited-user.json').as('editedUser')
  })

  it('creates a user successfully', function () {
    cy.server()

    cy.route({ url: '**/api/user', response: this.user })

    cy.route({ url: '**/api/current-tenant', response: this.tenant })

    cy.route({ url: '**/api/roles', response: this.roles })

    cy.route({ url: '**/api/companies/2', response: this.company })

    cy.visit('localhost:8080/user/edit-profile')

    cy.get('[data-cy=name-field]').clear({ force: true }).type('Bill Brasky', { force: true })

    cy.get('[data-cy=email-field]').clear({ force: true }).type('bill@example.com', { force: true })

    cy.get('[data-cy=position-field]').type('Software Developer', { force: true })

    cy.get('[data-cy=org-field]').type('TCompanies', { force: true })

    cy.route({ method: 'PUT', url: '**/api/users/**', response: this.editedUser })

    cy.route({ method: 'GET', url: '**/api/users', response: this.users })

    cy.get('[data-cy=save-button]').click({ force: true })

    cy.url().should('include', 'user/edit-profile')
  })
})
