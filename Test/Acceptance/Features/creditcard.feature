Feature: Credit card
    As an ecommerce admin
    I want to sell my products through transparent checkout for credit card purchases
    So my clients will have an facilitated payment option

    Scenario: Make a purchase by credit card
        Given a registered user
        When I access the store page
        And add any product to basket
        And I go to checkout page
        And login with registered user
        And choose pay with transparent checkout using credit card
        And I confirm my payment information with installment 1
        And place order
        Then the purchase must be paid with success

    Scenario: Make a purchase by credit card with installments
        Given a registered user
        When I access the store page
        And add any product to basket
        And I go to checkout page
        And login with registered user
        And choose pay with transparent checkout using credit card
        And I confirm my payment information with installment 10
        And place order
        Then the purchase must be paid with success
