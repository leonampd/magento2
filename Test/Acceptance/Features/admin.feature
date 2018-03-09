Feature: Admin
   As an ecommerce administrator
   I want to ensure that the pagarme module is installed correctly
   So I can sell my products via the best payment method solution

    Scenario: Pagar.me module should be installed
        Given an registered admin
        When I visit admin login page
        And I fill username input
        And I fill the password input
        And I click on login button
        Then I should see pagar.me as an item on left menu
