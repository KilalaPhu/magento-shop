<?php
namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;

class NewAction extends \Magento\Backend\App\Action
{
    protected $_resultForwardFactory = false;
    public function __construct
    (
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->_resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }
//    protected function _isAllowed()
//    {
//        return $this->_authorization->isAllowed('Mageplaza_GiftCard::add');
//    }

    public function execute()
    {
        $resultPage = $this->_resultForwardFactory->create();
        $resultPage->forward('edit');
        return $resultPage;
    }

}


