<?php
namespace Mageplaza\GiftCard\Controller\History;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\GiftCard\Model\ResourceModel\HistoryFactory;
use Magento\Framework\Registry;

class Index extends Action
{
    protected $_pageFactory;
    protected $_registry;
    protected $_history;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        HistoryFactory $historyFactory,Registry $registry)
    {
        $this->_pageFactory = $pageFactory;
        $this->_history = $historyFactory;
        $this->_registry= $registry;
        return parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->_pageFactory->create();
        return $result;
    }
}