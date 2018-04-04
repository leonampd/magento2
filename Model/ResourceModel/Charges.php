<?php


namespace PagarMe\Magento2\Model\ResourceModel;

class Charges extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('pagarme_magento2_charges', 'id');
    }
}
