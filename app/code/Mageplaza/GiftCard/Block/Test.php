<?php
namespace Mageplaza\GiftCard\Block;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\GiftCard\Model\CardFactory;
use Mageplaza\GiftCard\Helper\Data;
class Test extends \Magento\Framework\View\Element\Template
{
    protected $_registry;
    protected $_helper;
    protected $_cardfactory;
    public function __construct(Context $context,
                                Registry $registry,
                                CardFactory $cardFactory,Data $helper)
    {
        $this->_cardfactory = $cardFactory;
        $this->_registry = $registry;
        $this->_helper = $helper;
        parent::__construct($context);
    }
    public function getDulieu()
    {
        $model = $this->_cardfactory->create();
        $collection = $model->getCollection();
        $data = $collection->getData();
        return $data;
    }

    public function getLength()
    {
        $length = $this->_helper->getLength();
        return $length;
    }

}