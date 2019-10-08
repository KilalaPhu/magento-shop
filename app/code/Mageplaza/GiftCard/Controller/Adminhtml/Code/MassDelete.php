<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_Formbuilder
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\GiftCard\Model\ResourceModel\Card\CollectionFactory;


class MassDelete extends \Magento\Backend\App\Action
{
    protected $filter;
    protected $collectionFactory;
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }
    public function execute()
    {

        $collection = $this->collectionFactory->create();
        $data = $this->getRequest()->getParams();
        if (isset($data['selected'])) {
            $collection = $this->collectionFactory->create()->addFieldToFilter('giftcard_id', ['in' => $data['selected']]);
        }
        foreach ($collection as $rule) {
            $ids = $rule->getGiftcardId();
            if($ids) {
                $model= $this->_objectManager->create('Mageplaza\GiftCard\Model\Card');
                $model->load($ids);
                $model->delete();
            }
        }
        $this->messageManager->addSuccess(__('You have successfully deleted.'));

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageplaza_GiftCard::delete');
    }

}
