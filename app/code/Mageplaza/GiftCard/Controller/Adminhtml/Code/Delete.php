<?php
namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;
use Mageplaza\GiftCard\Model\CardFactory;
use Magento\Framework\Registry;

class Delete extends \Magento\Backend\App\Action
{

    protected $_resultPageFactory;
    protected $_card;
    protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\App\Action\Context $context,Registry $registry,CardFactory $card
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_card = $card;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    public function execute()
    {

        $id = $this->getRequest()->getParam('id');
        $model = $this->_card->create();
        $model->load($id)->delete();
        $this->messageManager->addSuccess('success');

        return $this->_redirect('*/*/index');
    }

}
