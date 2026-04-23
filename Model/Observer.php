<?php

namespace Meetanshi\DeferJS\Model;

use Magento\Framework\Event\ObserverInterface;
use Meetanshi\DeferJS\Helper\Data;

class Observer implements ObserverInterface
{
    /**
     * @var Data
     *
     * @SuppressWarnings(PHPMD.CamelCasePropertyName)
     */
    protected $_helper;

    /**
     * Observer constructor.
     *
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->_helper = $helper;
    }

    /**
     * Execute observer.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_helper->isEnabled()) {
            return;
        }
        $response = $observer->getEvent()->getData('response');
        if (!$response) {
            return;
        }
        $html = $response->getBody();
        if ($html == '') {
            return;
        }
        $conditionalJsPattern = '@(?:<script type="text/javascript"|<script)(.*)</script>@msU';
        preg_match_all($conditionalJsPattern, $html, $matches);
        $jsIf = implode('', $matches[0]);
        $html = preg_replace($conditionalJsPattern, '', $html);
        $html .= $jsIf;
        $response->setBody($html);
    }
}
