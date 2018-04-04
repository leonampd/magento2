<?php
/**
 * Class Billet
 */

namespace PagarMe\Magento2\Block\Payment\Info;

use Magento\Payment\Block\Info\Cc;

class BilletCreditCard extends Cc
{
    const TEMPLATE = 'PagarMe_Magento2::info/billetCreditCard.phtml';

    /**
     * {@inheritdoc}
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        $transport = new DataObject([
            (string)__('Print Billet') => $this->getInfo()->getAdditionalInformation('billet_url')
        ]);

        $transport = parent::_prepareSpecificInformation($transport);
        return $transport;
    }

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

    public function getCardLast4()
    {
        return '**** **** **** ' . $this->getInfo()->getAdditionalInformation('cc_last_4');
    }

    public function getCcBrand()
    {
        return $this->getInfo()->getAdditionalInformation('cc_type');
    }

    public function getTitle()
    {
        return $this->getInfo()->getAdditionalInformation('method_title');
    }

    public function getInstallments()
    {
        return $this->getInfo()->getAdditionalInformation('cc_installments');
    }

    public function getBilletUrl()
    {
        return $this->getInfo()->getAdditionalInformation('billet_url');
    }

    public function getCcAmount()
    {
        return $this->getInfo()->getAdditionalInformation('cc_cc_amount');
    }

    public function getCcAmountWithTax()
    {
        return $this->getInfo()->getAdditionalInformation('cc_cc_amount') + $this->getInfo()->getAdditionalInformation('cc_cc_tax_amount') / 100;
    }

    public function getBilletAmount()
    {
        return $this->getInfo()->getAdditionalInformation('cc_billet_amount');
    }
}
