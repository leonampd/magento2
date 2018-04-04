<?php
/**
 * Class Payment
 */

namespace PagarMe\Magento2\Block\Adminhtml\System\Config\Fieldset;


use Magento\Config\Block\System\Config\Form\Fieldset;

class Payment extends Fieldset
{
    /**
     * {@inheritdoc}
     */
    protected function _getFrontendClass($element)
    {
        return parent::_getFrontendClass($element) . ' with-button';
    }

    /**
     * {@inheritdoc}
     */
    protected function _getHeaderTitleHtml($element)
    {
        $html = '<div class="config-heading meli" ><div class="heading"><strong id="meli-logo">' . $element->getLegend();
        $html .= '</strong></div>';
        $html .= '<div class="button-container meli-cards"><button type="button"'
            . ' class="meli-payment-btn action-configure button'
            . '" id="' . $element->getHtmlId()
            . '-head" onclick="Fieldset.toggleCollapse(\'' . $element->getHtmlId() . '\', \''
            . $this->getUrl('*/*/state') . '\'); return false;"><span class="state-closed">'
            . __('Configure') . '</span><span class="state-opened">'
            . __('Close') . '</span></button></div></div>';
        return $html;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getHeaderCommentHtml($element)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    protected function _isCollapseState($element)
    {
        return false;
    }
}
