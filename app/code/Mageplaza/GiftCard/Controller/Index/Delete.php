<?php
namespace Mageplaza\GiftCard\Controller\Index;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\GiftCard\Model\CardFactory;

class Delete extends Action
{
    protected $_pageFactory;
    protected $_registry;
    protected $_cardfactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        CardFactory $cardFactory)
    {
        $this->_pageFactory = $pageFactory;
        $this->_cardfactory = $cardFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
            $model = $this->_cardfactory->create();
            $model->load($id)->delete();
        return $this->_redirect('giftcard/index/test');


    }
}