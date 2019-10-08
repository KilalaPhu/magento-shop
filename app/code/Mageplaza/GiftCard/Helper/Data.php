<?php
namespace Mageplaza\GiftCard\Helper;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Store\Model\StoreManagerInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $scopeConfig;
    protected $_groupCollection;
    protected $_storeManager;
    protected $_request;
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Helper\Context $context,
        StoreManagerInterface $storeManager)
    {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_request = $context->getRequest();
        $this->scopeConfig = $scopeConfig;
    }
    public function getLength()
    {
        return $this->scopeConfig->getValue('giftcard/code/code_configuration',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);

    }

    public function getRedeem()
    {
        return $this->scopeConfig->getValue('giftcard/general/enable_redeem',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);

    }



}





