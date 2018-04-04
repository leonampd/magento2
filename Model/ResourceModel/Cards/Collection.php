<?php


namespace PagarMe\Magento2\Model\ResourceModel\Cards;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'PagarMe\Magento2\Model\Cards',
            'PagarMe\Magento2\Model\ResourceModel\Cards'
        );
    }
}
