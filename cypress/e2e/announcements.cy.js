
/*
 * References:
 * Cypress Documentation - https://docs.cypress.io/
 * cy.get() - https://docs.cypress.io/api/commands/get
 * cy.env() - https://docs.cypress.io/api/commands/env
 */

describe('Announcement Testing', () => {

    it('should allow employer to post an announcement', () => {

        cy.visit('http://localhost:8888/capstone-Simranaujla/pages/login.php')

        cy.env(['employerEmail', 'employerPassword']).then(({ employerEmail, employerPassword }) => {

            // Login as employer
            cy.get('input[name="email"]').type(employerEmail)
            cy.get('input[name="password"]').type(employerPassword, { log: false })
            cy.get('button[type="submit"]').click()

            cy.url().should('include', 'dashboard.php')

            // Open announcements page
            cy.visit('http://localhost:8888/capstone-Simranaujla/pages/announcements.php')

            // Enter announcement details
            cy.get('input[name="title"]').type('Cypress Test Announcement')
            cy.get('textarea[name="message"]').type('Testing announcement functionality.')

            cy.contains('button', 'Post Announcement').click()

            // Verify announcement was posted
            cy.contains('Announcement posted successfully').should('be.visible')
            cy.contains('Cypress Test Announcement').should('be.visible')

        })

    })

    it('should reject empty announcement fields', () => {

        cy.visit('http://localhost:8888/capstone-Simranaujla/pages/login.php')

        cy.env(['employerEmail', 'employerPassword']).then(({ employerEmail, employerPassword }) => {

            // Login as employer
            cy.get('input[name="email"]').type(employerEmail)
            cy.get('input[name="password"]').type(employerPassword, { log: false })
            cy.get('button[type="submit"]').click()

            cy.url().should('include', 'dashboard.php')

            cy.visit('http://localhost:8888/capstone-Simranaujla/pages/announcements.php')

            // Submit empty announcement
            cy.contains('button', 'Post Announcement').click()

            // Verify validation message
            cy.contains('Title and announcement message are required.').should('be.visible')

        })

    })

    it('should allow employee to view announcements but not create them', () => {

        cy.visit('http://localhost:8888/capstone-Simranaujla/pages/login.php')

        cy.env(['testEmail', 'testPassword']).then(({ testEmail, testPassword }) => {

            // Login as employee
            cy.get('input[name="email"]').type(testEmail)
            cy.get('input[name="password"]').type(testPassword, { log: false })
            cy.get('button[type="submit"]').click()

            cy.url().should('include', 'dashboard.php')

            cy.visit('http://localhost:8888/capstone-Simranaujla/pages/announcements.php')

            // Verify employee can view announcements
            cy.contains('Cypress Test Announcement').should('be.visible')
            cy.contains('Testing announcement functionality.').should('be.visible')

            // Verify employee cannot create announcements
            cy.get('input[name="title"]').should('not.exist')
            cy.get('textarea[name="message"]').should('not.exist')
            cy.contains('button', 'Post Announcement').should('not.exist')

        })

    })

})
