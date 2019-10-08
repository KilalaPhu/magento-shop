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
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Mageplaza\GiftCard\Model\CardFactory;
use Magento\Framework\Math\Random;
class Save extends Action
{
    protected $_card;
    protected $_random;
    public function __construct(Context $context,
                                CardFactory $cardFactory,
                                Random $random)
    {
        parent::__construct($context);
        $this->_card = $cardFactory;
        $this->_random = $random;
    }
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $code_length = $this->getRequest()->getParam('code_length');

        $code = $this->_random->getRandomString($code_length, 'ABCDEFGHIJKLMLOPQRSTUVXYZ0123456789');
        $model = $this->_card->create();
        $check_code = $model->getCollection()->addFieldToFilter('code',['eq'=>$code])->getData();

        $resultRedirect = $this->resultRedirectFactory->create();

        if(!empty($data['giftcard_id'])){
            $id = $data['giftcard_id'];
            $model = $this->_card->create();
            if (empty($model->load($id)->getData())){
                $this->messageManager->addError('loi roi');
                return $resultRedirect->setPath('*/*/');
            }
            $model->addData($data);
            $model->save();

            if ($this->getRequest()->getParam('back')) {
                $this->messageManager->addSuccess('You Save and Continue the Gift Card.');
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
            $this->messageManager->addSuccess('You saved the Gift Card.');
        }else{
            if(!empty($check_code)){
                $this->messageManager->addError(__('code already exists'));
                return $resultRedirect->setPath('*/*/');
            }else{
                $model = $this->_card->create();
                $model->addData([
                    'code' => $code,
                    'balance' => $data['balance'],
                    'create_from' => $data['create_from']
                ]);
                $model->save();



                $id = $model->getId();
                if ($this->getRequest()->getParam('back')) {
                    $this->messageManager->addSuccess('You Save and Continue the Gift Card.');
                    return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
                }else{
                    $this->messageManager->addSuccess('You have added Gift Card.');
                }
            }
        }
        return $resultRedirect->setPath('*/*/');

    }
}
