<?php

namespace PagarMe\Magento2\Model\Source;

class BilletTypes extends \Magento\Payment\Model\Source\Cctype
{
    /**
     * @return array
     */
    public function getAllowedTypes()
    {
        return [
            'Itau',
            'Bradesco',
            'Santander',
            'CitiBank',
            'BancoDoBrasil',
            'Caixa',
            'Stone'
        ];
    }
}
