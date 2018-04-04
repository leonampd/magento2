<?php


namespace PagarMe\Magento2\Api\Data;

interface CardsSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Cards list.
     * @return \PagarMe\Magento2\Api\Data\CardsInterface[]
     */
    public function getItems();

    /**
     * Set content list.
     * @param \PagarMe\Magento2\Api\Data\CardsInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
