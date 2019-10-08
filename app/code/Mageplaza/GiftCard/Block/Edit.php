<?php
namespace Mageplaza\GiftCard\Block;
use Magento\Framework\Registry;
use Mageplaza\GiftCard\Model\CardFactory;

use Magento\Framework\View\Element\Template\Context;
class Edit extends \Magento\Framework\View\Element\Template
{
    protected $_registry;
    protected $_cardfactory;

    public function __construct(Context $context,Registry $registry,CardFactory $cardFactory)
    {
        $this->_registry = $registry;
        $this->_cardfactory = $cardFactory;
        parent::__construct($context);
    }
    public function getDulieu()
    {
        $id=$this->_registry->registry('id');
        $model = $this->_cardfactory->create();
        $model->load($id);
        $data = $model->getData();
        return $data;
    }

}