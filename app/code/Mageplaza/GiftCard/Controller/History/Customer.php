<?php
namespace Mageplaza\GiftCard\Controller\History;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\GiftCard\Model\ResourceModel\HistoryFactory;
use Magento\Customer\Model\Session;
class Customer extends Action
{
    protected $_pageFactory;
    protected $_registry;
    protected $_history;
    protected $_session;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        HistoryFactory $historyFactory,Session $session)
    {
        $this->_pageFactory = $pageFactory;
        $this->_history = $historyFactory;
        $this->_session = $session;
        return parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->_pageFactory->create();

        $id = $this->_session->getCustomerId();
        if(!empty($id)){
            return $result;
        }
        else{
return $this->_redirect('customer/account/login',['referer'=>'aHR0cDovL2xvY2FsaG9zdC5jb20vbWFnZW50bzIzMS9jdXN0b21lci9hY2NvdW50L2xvZ291dFN1Y2Nlc3Mv'])  ;
        }

    }
}