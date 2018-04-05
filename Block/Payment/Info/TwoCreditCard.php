<?php
/**
 * Class Billet
 */

namespace PagarMe\Magento2\Block\Payment\Info;

use Magento\Payment\Block\Info\Cc;

class TwoCreditCard extends Cc
{
    const TEMPLATE = 'PagarMe_Magento2::info/twoCreditCard.phtml';

    public function _construct()
    {
        $this->setTemplate(self::TEMPLATE);
    }

    public function getCcType()
    {
        return $this->getCcTypeName();
    }

    public function getCardNumber()
    {
        return '**** **** **** ' . $this->getInfo()->getCcLast4();
    }

    public function getTitle()
    {
        return $this->getInfo()->getAdditionalInformation('method_title');
    }

    public function getInstallments()
    {
        return $this->getInfo()->getAdditionalInformation('cc_installments');
    }

    public function getInstallmentsFirstCard()
    {
        return $this->getInfo()->getAdditionalInformation('cc_installments_first');
    }

    public function getCcTypeFirst()
    {
        return $this->getInfo()->getAdditionalInformation('cc_type_first');
    }

    public function getFirstCardAmount()
    {
        return $this->getInfo()->getAdditionalInformation('cc_first_card_amount') + $this->getInfo()->getAdditionalInformation('cc_first_card_tax_amount');
    }

    public function getFirstCardLast4()
    {
        return '**** **** **** ' . $this->getInfo()->getAdditionalInformation('cc_last_4_first');
    }

    public function getInstallmentsSecondCard()
    {
        return $this->getInfo()->getAdditionalInformation('cc_installments_second');
    }

    public function getCcTypeSecond()
    {
        return $this->getInfo()->getAdditionalInformation('cc_type_second');
    }

    public function getSecondCardAmount()
    {
        return $this->getInfo()->getAdditionalInformation('cc_second_card_amount') + $this->getInfo()->getAdditionalInformation('cc_second_card_tax_amount');
    }

    public function getSecondCardLast4()
    {
        return '**** **** **** ' . $this->getInfo()->getAdditionalInformation('cc_last_4_second');
    }
}
