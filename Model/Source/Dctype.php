<?php

namespace PagarMe\Magento2\Model\Source;

/**
 * CC Types
 */

class Dctype extends \Magento\Payment\Model\Source\Cctype
{
    /**
     * @return array
     */
    public function getAllowedTypes()
    {
        return [
            'Simulado',
            'Cielo-Visa',
            'Cielo-Master',
            'Cielo-Elo',
            'Redecard-Visa',
            'Redecard-Master'
        ];
    }
}
