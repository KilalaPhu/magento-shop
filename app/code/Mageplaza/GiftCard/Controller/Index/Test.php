<?php
namespace Mageplaza\GiftCard\Controller\Index;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\GiftCard\Model\CardFactory;
use Magento\Framework\Registry;
use Magento\Checkout\Model\Session;


class Test extends Action
{
    protected $_pageFactory;
    protected $_registry;
    protected $_cardfactory;
    protected $_session;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        CardFactory $cardFactory,Registry $registry,Session $session)
    {
        $this->_pageFactory = $pageFactory;
        $this->_cardfactory = $cardFactory;
        $this->_registry= $registry;
        $this->_session= $session;
        return parent::__construct($context);
    }

    public function execute()
    {

        $result = $this->_pageFactory->create();
        return $result;
    }
}