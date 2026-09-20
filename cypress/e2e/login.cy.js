/*
 * References:
 * Cypress Documentation - https://docs.cypress.io/
 * cy.get() - https://docs.cypress.io/api/commands/get
 * cy.env() - https://docs.cypress.io/api/commands/env
 */

describe('Login Tests', () => {

  // TC-F1: Valid Login
  it('should login successfully with valid credentials', () => {

    cy.visit('http://localhost:8888/capstone-Simranaujla/pages/login.php')

    cy.env(['testEmail', 'testPassword']).then(({ testEmail, testPassword }) => {

      cy.get('input[name="email"]').type(testEmail)
      cy.get('input[name="password"]').type(testPassword)

      cy.get('button[type="submit"]').click()

      cy.url().should('include', 'dashboard.php')
    })
  })


  // TC-F2: Invalid Login
  it('should show an error for invalid login', () => {

    cy.visit('http://localhost:8888/capstone-Simranaujla/pages/login.php')

    cy.env(['testEmail']).then(({ testEmail }) => {

      cy.get('input[name="email"]').type(testEmail)
      cy.get('input[name="password"]').type('Wrong@123')

      cy.get('button[type="submit"]').click()

      cy.contains('Invalid').should('be.visible')
    })
  })

})