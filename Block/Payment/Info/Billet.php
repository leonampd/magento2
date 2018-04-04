<?php
/**
 * Class Billet
 */

namespace PagarMe\Magento2\Block\Payment\Info;


use Magento\Payment\Block\Info;
use Magento\Framework\DataObject;

class Billet extends Info
{
    const TEMPLATE = 'PagarMe_Magento2::info/billet.phtml';

    public function _construct()
    {
        $this->setTemplate(self::TEMPLATE);
    }

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

    public function getBilletUrl()
    {
        return $this->getInfo()->getAdditionalInformation('billet_url');
    }

    public function getTitle()
    {
        return $this->getInfo()->getAdditionalInformation('method_title');
    }
}
