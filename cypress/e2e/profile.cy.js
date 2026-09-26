/*
 * References:
 * Cypress Documentation - https://docs.cypress.io/
 * cy.get() - https://docs.cypress.io/api/commands/get
 * cy.env() - https://docs.cypress.io/api/commands/env
 */

describe('TC-F4 Profile Update', () => {

    it('should update the employee phone number successfully', () => {

        // Open login page
        cy.visit('http://localhost:8888/capstone-Simranaujla/pages/login.php')

        // Get test credentials
        cy.env(['testEmail', 'testPassword']).then(({ testEmail, testPassword }) => {

            // Login as employee
            cy.get('input[name="email"]').type(testEmail)
            cy.get('input[name="password"]').type(testPassword)

            cy.get('button[type="submit"]').click()

            // Verify successful login
            cy.url().should('include', 'dashboard.php')

            // Open employee profile page
            cy.visit('http://localhost:8888/capstone-Simranaujla/pages/profile.php')

            // Verify profile page opened
            cy.url().should('include', 'profile.php')

            // Update phone number
            cy.get('input[name="phone"]')
            .clear()
            .type('9876543210')

            // Submit profile update
            cy.get('button[type="submit"]').click()

            // Verify successful update
            cy.contains('Profile updated successfully').should('be.visible')
            
            // Verify updated phone number is saved
            cy.get('input[name="phone"]')
            .should('have.value', '9876543210')

        })

    })

})