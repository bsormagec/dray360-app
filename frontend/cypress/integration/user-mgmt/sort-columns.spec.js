describe('Sorts columns', function () {
  beforeEach(() => {
    cy.fixture('auth/tenant.json').as('tenant')
    cy.fixture('auth/user.json').as('user')
    cy.fixture('user-mgmt/roles.json').as('roles')
    cy.fixture('user-mgmt/company.json').as('company')
    cy.fixture('user-mgmt/user-list.json').as('users')
    cy.fixture('user-mgmt/single-user-response.json').as('singleUser')
    cy.fixture('user-mgmt/edited-user.json').as('editedUser')
  })

  it('sorts columns successfully', function () {
    cy.server()

    cy.route({ url: '**/api/user', response: this.user })

    cy.route({ url: '**/api/current-tenant', response: this.tenant })

    cy.route({ url: '**/api/users**', response: this.users })

    cy.route({ url: '**/api/roles', response: this.roles })

    cy.route({ url: '**/api/companies/2', response: this.company })

    cy.visit('http://localhost:8080/user/dashboard')

    cy.wait(1000)

    cy.get('thead>tr>th').eq(1).click({ force: true })
    cy.get('thead>tr>th').eq(2).click({ force: true })
    cy.get('thead>tr>th').eq(3).click({ force: true })
    cy.get('thead>tr>th').eq(4).click({ force: true })
    cy.get('thead>tr>th').eq(5).click({ force: true })
  })

  it('searches successfully', function () {
    cy.server()

    cy.route({ url: '**/api/user', response: this.user })

    cy.route({ url: '**/api/current-tenant', response: this.tenant })

    cy.route({ url: '**/api/users**', response: this.users })

    cy.route({ url: '**/api/roles', response: this.roles })

    cy.route({ url: '**/api/companies/2', response: this.company })

    cy.visit('http://localhost:8080/user/dashboard')

    cy.wait(1000)

    cy.route({ url: '**/api/users**', response: this.singleUser })

    cy.get('[data-cy=dashboard-search]').type('Doe', { force: true })

    cy.get('tbody>tr').should('have.length', 1)
  })

  it('selects columns successfully', function () {
    cy.server()

    cy.route({ url: '**/api/user', response: this.user })

    cy.route({ url: '**/api/current-tenant', response: this.tenant })

    cy.route({ url: '**/api/users**', response: this.users })

    cy.route({ url: '**/api/roles', response: this.roles })

    cy.route({ url: '**/api/companies/2', response: this.company })

    cy.visit('http://localhost:8080/user/dashboard')

    cy.wait(1000)

    cy.get('[data-cy=column-select]').click({ force: true })

    cy.get('div>div>i').eq(4).click({ force: true })

    cy.get('thead>tr>th>span').should('have.length', 5)
  })
})
