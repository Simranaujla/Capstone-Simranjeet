describe('TC-F3 Punch In', () => {

    it('should allow an employee to punch in successfully', () => {
        //Open login page
        cy.visit('http://localhost:8888/capstone-Simranaujla/pages/login.php')

        //Get employee test crendentials
        cy.env(['testEmail','testPassword']).then(({testEmail,testPassword}) => {
            //Login as employee
            cy.get('input[name = "email"]').type(testEmail)
            cy.get('input[name="password"]').type(testPassword)

            cy.get('button[type="submit"]').click()

            //verify successful login
            cy.url().should('include','dashboard.php')

            // Open Time Tracking page
            cy.visit('http://localhost:8888/capstone-Simranaujla/pages/time_tracking.php')

            // Verify Time Tracking page opened
            cy.url().should('include', 'time_tracking.php')

            // Click Punch In button
            cy.get('button[value="punch_in"]').click()

            // Verify successful punch in
            cy.contains('Punch In successful.').should('be.visible')

            // Verify today's punch-in record appears in the table
            const today = new Date().toISOString().split('T')[0]

            cy.get('table').should('contain', today)
        })

    })

})