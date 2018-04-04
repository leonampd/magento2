<?php
/**
 * Class Validator
 */

namespace PagarMe\Magento2\Gateway\Transaction\BilletCreditCard\ResourceGateway\Capture\Response;


use Magento\Payment\Gateway\Validator\ValidatorInterface;
use PagarMe\Magento2\Gateway\Transaction\Base\ResourceGateway\Response\AbstractValidator;

class Validator extends AbstractValidator implements ValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function validate(array $validationSubject)
    {
        if (!isset($validationSubject['response'])) {
            throw new \InvalidArgumentException('PagarMe Credit Card Capture Response object should be provided');
        }

        $isValid = true;
        $fails = [];

        return $this->createResult($isValid, $fails);
    }
}
