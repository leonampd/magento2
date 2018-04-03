/**
 * @author      MundiPagg Embeddables Team <embeddables@mundipagg.com>
 * @copyright   2017 MundiPagg (http://www.mundipagg.com)
 * @license     http://www.mundipagg.com  Copyright
 *
 * @link        http://www.mundipagg.com
 */
/*browser:true*/
/*global define*/
define(
    [
        'PagarMe_Magento2/js/view/payment/cc-form',
        'ko',
        'PagarMe_Magento2/js/action/installments',
        'PagarMe_Magento2/js/action/installmentsByBrand',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function (
        Component,
        ko,
        installments,
        installmentsByBrand,
        $,
        quote,
        priceUtils,
        totals,
        checkoutData, 
        selectPaymentMethodAction, 
        fullScreenLoader
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'PagarMe_Magento2/payment/creditcard',
                creditCardType: '',
                creditCardInstallments: '',
                creditCardOwner: '',
                creditCardExpYear: '',
                creditCardExpMonth: '',
                creditCardsavecard: 0,
                creditCardNumber: '',
                creditCardSsStartMonth: '',
                creditCardSsStartYear: '',
                creditCardSsIssue: '',
                creditCardVerificationNumber: '',
                creditSavedCard: window.checkoutConfig.payment.pagarme_creditcard.selected_card,
                selectedCardType: null,
                allInstallments: ko.observableArray([])
            },

            totals: quote.getTotals(),

            initialize: function () {
                this._super();

                this.getCcInstallments();

                var self = this;

                this.getInstallmentsByBrand = function (brand) {

                    $.when(
                        installmentsByBrand(brand)
                    ).done(function (data) {
                        self.allInstallments.removeAll();

                        _.map(data, function (value, key) {
                            self.allInstallments.push({
                                'value': value.id,
                                'interest': value.interest,
                                'installments': value.label
                            });
                        });

                    }).always(function () {
                        //fullScreenLoader.stopLoader();
                    });

                }

                if (this.creditSavedCard()) {

                    var brand = this.creditSavedCard();
                    self.getInstallmentsByBrand(brand);

                }

                this.creditCardType.subscribe(function (brand) {

                    self.getInstallmentsByBrand(brand);

                });

                this.creditSavedCard.subscribe(function(value){
                    if (typeof value != 'undefined') {
                        var cards = window.checkoutConfig.payment.pagarme_creditcard.cards;
                        for (var i = 0, len = cards.length; i < len; i++) {
                            if(cards[i].id == value){
                                self.creditCardSavedNumber(window.checkoutConfig.payment.pagarme_creditcard.cards[i].last_four_numbers);
                                self.creditCardType(window.checkoutConfig.payment.pagarme_creditcard.cards[i].brand);
                            }
                        }
                    }
                });

            },

            /**
             * Select current payment token
             */
            selectPaymentMethod: function () {
                this.oldInstallmentTax = window.checkoutConfig.payment.ccform.installments.value;
                var newTax = 0;

                var total = quote.getTotals()();
                var subTotalIndex = null;
                for (var i = 0, len = total.total_segments.length; i < len; i++) {
                    if (total.total_segments[i].code == "grand_total") {
                        subTotalIndex = i;
                        continue;
                    }
                    if (total.total_segments[i].code != "tax") continue;
                    total.total_segments[i].value = newTax;
                }

                total.total_segments[subTotalIndex].value = +total.total_segments[subTotalIndex].value - this.oldInstallmentTax;
                total.total_segments[subTotalIndex].value = +total.total_segments[subTotalIndex].value + parseFloat(newTax);
                total.tax_amount = parseFloat(newTax);
                total.base_tax_amount = parseFloat(newTax);
                this.oldInstallmentTax = newTax;
                window.checkoutConfig.payment.ccform.installments.value = newTax;
                quote.setTotals(total);

                selectPaymentMethodAction(this.getData());
                checkoutData.setSelectedPaymentMethod(this.item.method);
                $("#pagarme_creditcard_installments").val('');

                return true;
            },

            /**
             * Get payment method data
             */
            getData: function () {
                return {
                    'method': this.item.method,
                    'po_number': null,
                    'additional_data': null
                };
            },

            initObservable: function () {
                this._super()
                    .observe([
                        'creditCardType',
                        'creditCardSavedNumber',
                        'creditCardOwner',
                        'creditCardExpYear',
                        'creditCardExpMonth',
                        'creditCardNumber',
                        'creditCardVerificationNumber',
                        'creditCardSsStartMonth',
                        'creditCardSsStartYear',
                        'creditCardsavecard',
                        'creditCardSsIssue',
                        'creditSavedCard',
                        'selectedCardType',
                        'creditCardInstallments',
                    ]);
                return this;
            },

            getCode: function () {
                return 'pagarme_creditcard';
            },

            isActive: function () {
                return window.checkoutConfig.payment.pagarme_creditcard.active;
            },

            isInstallmentsActive: function () {
                return window.checkoutConfig.payment.ccform.installments.active[this.getCode()];
            },

            getCcInstallments: function () {
                var self = this;

                fullScreenLoader.startLoader();
                $.when(
                    installments()
                ).done(function (transport) {
                    self.allInstallments.removeAll();

                    _.map(transport, function (value, key) {
                        self.allInstallments.push({
                            'value': value.id,
                            'interest': value.interest,
                            'installments': value.label
                        });
                    });

                }).always(function () {
                    fullScreenLoader.stopLoader();
                });
            },

            setInterest: function (option, item) {
                if (typeof item != 'undefined') {
                    ko.applyBindingsToNode(option, {
                        attr: {
                            interest: item.interest
                        }
                    }, item);
                }

            },

            getCcInstallmentsValues: function () {
                return _.map(this.getCcInstallments(), function (value, key) {
                    return {
                        'value': key,
                        'installments': value
                    };
                });
            },

            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'cc_cid': this.creditCardVerificationNumber(),
                        'cc_type': this.creditCardType(),
                        'cc_last_4': this.creditCardSavedNumber() ? this.creditCardSavedNumber() : this.creditCardNumber(),
                        'cc_exp_year': this.creditCardExpYear(),
                        'cc_exp_month': this.creditCardExpMonth(),
                        'cc_number': this.creditCardNumber(),
                        'cc_owner': this.creditCardOwner(),
                        'cc_savecard': this.creditCardsavecard() ? 1 : 0,
                        'cc_saved_card': this.creditSavedCard(),
                        'cc_installments': this.creditCardInstallments(),
                    }
                };
            },

            onInstallmentItemChange: function () {
                this.updateTotalWithTax(jQuery('#pagarme_creditcard_installments option:selected').attr('interest'));
            },

            updateTotalWithTax: function (newTax) {
                if (typeof this.oldInstallmentTax == 'undefined') {
                    this.oldInstallmentTax = 0;
                }
                // console.log(newTax);
                var total = quote.getTotals()();
                var subTotalIndex = null;
                for (var i = 0, len = total.total_segments.length; i < len; i++) {
                    if (total.total_segments[i].code == "grand_total") {
                        subTotalIndex = i;
                        continue;
                    }
                    if (total.total_segments[i].code != "tax") continue;
                    total.total_segments[i].value = newTax;
                }

                total.total_segments[subTotalIndex].value = +total.total_segments[subTotalIndex].value - this.oldInstallmentTax;
                total.total_segments[subTotalIndex].value = +total.total_segments[subTotalIndex].value + parseFloat(newTax);
                total.tax_amount = parseFloat(newTax);
                total.base_tax_amount = parseFloat(newTax);
                this.oldInstallmentTax = newTax;
                window.checkoutConfig.payment.ccform.installments.value = newTax;
                quote.setTotals(total);
            },

            onSavedCardChange: function () {
                if (jQuery('#pagarme_creditcard_card').val()) {
                    jQuery('#pagarme_creditcard_cc_icons').css('display', 'none');
                    jQuery('#pagarme_creditcard_cc_savecard').css('display', 'none');
                    jQuery('#pagarme_creditcard_cc_number_div').css('display', 'none');
                    jQuery('#pagarme_creditcard_cc_owner_div').css('display', 'none');
                    jQuery('#pagarme_creditcard_cc_type_exp_div').css('display', 'none');
                    jQuery('#pagarme_creditcard_cc_type_cvv_div').css('display', 'none');
                } else {
                    jQuery('#pagarme_creditcard_cc_icons').css('display', 'block');
                    jQuery('#pagarme_creditcard_cc_savecard').css('display', 'block');
                    jQuery('#pagarme_creditcard_cc_number_div').css('display', 'block');
                    jQuery('#pagarme_creditcard_cc_owner_div').css('display', 'block');
                    jQuery('#pagarme_creditcard_cc_type_exp_div').css('display', 'block');
                    jQuery('#pagarme_creditcard_cc_type_cvv_div').css('display', 'block');
                }
            },
        })
    }
);
