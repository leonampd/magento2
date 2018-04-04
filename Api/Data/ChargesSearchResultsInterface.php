<?php


namespace PagarMe\Magento2\Api\Data;

interface ChargesSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Charges list.
     * @return \PagarMe\Magento2\Api\Data\ChargesInterface[]
     */
    public function getItems();

    /**
     * Set content list.
     * @param \PagarMe\Magento2\Api\Data\ChargesInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
