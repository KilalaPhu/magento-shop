<?php
namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;
use Mageplaza\GiftCard\Model\CardFactory;
use Magento\Framework\Registry;

class Edit extends \Magento\Backend\App\Action
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

        if ($id) {
            $model->load($id);
            $data = $model->getData();
            $this->_coreRegistry->register('data', $data);
        }

        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend($model->getGiftcardId() ? __('Edit GiftCard') : __('New GiftCard'));

        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageplaza_GiftCard::edit');
    }

}
