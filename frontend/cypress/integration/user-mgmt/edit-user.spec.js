// describe('Edit user', function () {
//   beforeEach(() => {
//     cy.fixture('auth/tenant.json').as('tenant')
//     cy.fixture('auth/user.json').as('user')
//     cy.fixture('user-mgmt/roles.json').as('roles')
//     cy.fixture('user-mgmt/company.json').as('company')
//     cy.fixture('user-mgmt/user-list.json').as('users')
//     cy.fixture('user-mgmt/edited-user.json').as('editedUser')
//   })

//   it('edits a user successfully', function () {
//     cy.server()

//     cy.route({ url: '**/api/user', response: this.user })

//     cy.route({ url: '**/api/current-tenant', response: this.tenant })

//     cy.route({ url: '**/api/roles', response: this.roles })

//     cy.route({ url: '**/api/companies/2', response: this.company })

//     cy.visit('http://localhost:8080/user/dashboard/edit-user/1')

//     cy.get('[data-cy=name-input]').type('Bill Brasky')

//     cy.get('[data-cy=email-input]').type('bill@example.com')

//     cy.route({ method: 'PUT', url: '**/api/users/**', response: this.editedUser })

//     cy.route({ method: 'GET', url: '**/api/users', response: this.users })

//     cy.get('[data-cy=save-button]').click()

//     cy.url().should('include', 'user/dashboard')
//   })
// })
