Feature: Billet 
    As an ecommerce admin
    I want to sell my products through transparent checkout for billet purchases
    So my clients will have an extra options. Also, clients that doesn't have credit cards or bank accounts can purchase my products

    Scenario: Make a purchase by billet
        Given a registered user
        When I access the store page
        And add any product to basket
        And I go to checkout page
        And login with registered user
        And choose pay with transparent checkout billet
        And place order
        Then the purchase must be paid with success
