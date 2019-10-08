<?php
namespace Mageplaza\GiftCard\Controller\Index;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\GiftCard\Model\CardFactory;
use Magento\Framework\Registry;

class Edit extends Action
{
    protected $_pageFactory;
    protected $_registry;
    protected $_cardfactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        CardFactory $cardFactory,Registry $registry)
    {
        $this->_pageFactory = $pageFactory;
        $this->_cardfactory = $cardFactory;
        $this->_registry= $registry;
        return parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $this->_registry->register('id',$id);
        $result = $this->_pageFactory->create();
        return $result;


    }
}