<?php

namespace PagarMe\Magento2\Model\Source;

/**
 * CC Types
 */

class Cctype extends \Magento\Payment\Model\Source\Cctype
{
    /**
     * @return array
     */
    public function getAllowedTypes()
    {
        return [
			'Visa',
			'Mastercard',
			'Amex',
			'Hipercard',
			'Diners',
			'Elo',
			'Discover',
			'Aura',
			'JCB',
			'Credz',
			'SodexoAlimentacao',
			'SodexoCultura',
			'SodexoGift',
			'SodexoPremium',
			'SodexoRefeicao',
			'SodexoCombustivel',
			'VR',
			'Alelo',
			'Banese',
			'Cabal',
        ];
    }
}
